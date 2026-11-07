<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'student_id' => ['required', 'string', 'regex:/^TUPT-[0-9]{2}-[0-9]{4}$/'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', strtolower($validated['email']))
            ->where('student_id', strtoupper($validated['student_id']))
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'error' => [
                    'code' => 'INVALID_CREDENTIALS',
                    'message' => 'Invalid email or password.',
                ],
            ], 401);
        }

        if (!in_array($user->role, ['student', 'acso_officer'], true)) {
            return response()->json([
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'This account is not authorized for mobile access.',
                ],
            ], 403);
        }

        if ($user->email_verified_at === null && $user->status === 'pending' && $user->otp_registration_id !== null) {
            return response()->json([
                'error' => [
                    'code' => 'EMAIL_NOT_VERIFIED',
                    'message' => 'Your account is saved but your TUP email is not verified yet.',
                ],
                'registration_id' => $user->otp_registration_id,
            ], 403);
        }

        if ($user->status === 'rejected') {
            return response()->json([
                'error' => [
                    'code' => 'ID_REJECTED',
                    'message' => 'Your student ID could not be approved. Contact SAMI support for help.',
                ],
                'registration_id' => $user->otp_registration_id,
            ], 403);
        }

        if ($user->status !== 'active' || $user->email_verified_at === null) {
            return response()->json([
                'error' => [
                    'code' => 'ACCOUNT_PENDING_REVIEW',
                    'message' => 'Your student ID is awaiting eligibility review.',
                ],
            ], 403);
        }

        $token = $user->createToken('sami-mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ]);
    }
}
