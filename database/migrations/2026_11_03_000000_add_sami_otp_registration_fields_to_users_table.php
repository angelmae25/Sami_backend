<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('student_id', 25)->nullable()->unique();
            $table->string('status', 20)->default('active')->index();
            $table->string('otp_registration_id', 36)->nullable()->unique();
            $table->string('otp_hash')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('otp_last_sent_at')->nullable();
            $table->unsignedTinyInteger('otp_attempts')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique('users_student_id_unique');
            $table->dropUnique('users_otp_registration_id_unique');
            $table->dropIndex('users_status_index');
            $table->dropColumn([
                'student_id',
                'status',
                'otp_registration_id',
                'otp_hash',
                'otp_expires_at',
                'otp_last_sent_at',
                'otp_attempts',
            ]);
        });
    }
};