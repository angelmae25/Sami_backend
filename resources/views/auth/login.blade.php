<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Administrator Sign In · SAMI</title>
    <style>
        :root {
            color-scheme: light;
            --wine: #6D0F22;
            --wine-deep: #4b0918;
            --rose: #B18D94;
            --blush: #F5E4E8;
            --canvas: #F9F6F7;
            --white: #FFFFFF;
            --ink: #2B2023;
            --muted: #776a6d;
            --line: #e7dcdf;
            --ease-out: cubic-bezier(0.23, 1, 0.32, 1);
            --ease-in-out: cubic-bezier(0.77, 0, 0.175, 1);
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            background: var(--canvas);
            color: var(--ink);
            font-family: Inter, "Segoe UI", -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        button, input { font: inherit; }

        .login-shell {
            display: grid;
            grid-template-columns: minmax(440px, 0.96fr) minmax(500px, 1.04fr);
            width: min(1440px, 100%);
            min-height: 100vh;
            margin: 0 auto;
            background: var(--white);
            box-shadow: 0 24px 80px rgba(43, 32, 35, 0.08);
            animation: shell-arrive 280ms var(--ease-out) both;
        }

        .brand-panel {
            position: relative;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: clamp(34px, 5vw, 68px) clamp(34px, 5.4vw, 78px) 34px;
            isolation: isolate;
            color: var(--white);
            background: linear-gradient(145deg, #76172b 0%, var(--wine) 48%, var(--wine-deep) 100%);
        }

        .brand-panel::before, .brand-panel::after {
            position: absolute;
            z-index: -1;
            border: 1px solid rgba(255, 255, 255, 0.11);
            border-radius: 50%;
            content: "";
            pointer-events: none;
        }

        .brand-panel::before {
            top: 8%;
            right: -245px;
            width: 550px;
            aspect-ratio: 1;
            box-shadow: 0 0 0 42px rgba(255,255,255,.025), 0 0 0 88px rgba(255,255,255,.02);
        }

        .brand-panel::after {
            bottom: 11%;
            left: -190px;
            width: 370px;
            aspect-ratio: 1;
            background: rgba(255, 255, 255, 0.025);
        }

        .brand-lockup, .brand-copy, .workspace-card, .brand-footer,
        .form-heading, .login-form, .secure-note {
            opacity: 0;
            animation: content-arrive 250ms var(--ease-out) forwards;
        }

        .brand-lockup { animation-delay: 70ms; }
        .brand-copy { animation-delay: 130ms; }
        .workspace-card { animation-delay: 190ms; }
        .brand-footer { animation-delay: 260ms; }
        .form-heading { animation-delay: 110ms; }
        .login-form { animation-delay: 170ms; }
        .secure-note { animation-delay: 230ms; }

        .brand-lockup { display: inline-flex; align-items: center; gap: 13px; width: fit-content; }
        .brand-mark {
            display: grid;
            width: 46px;
            height: 46px;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .44);
            border-radius: 13px;
            background: rgba(255, 255, 255, .09);
            box-shadow: inset 0 1px rgba(255, 255, 255, .15);
        }
        .brand-mark svg { width: 27px; height: 27px; }
        .brand-name { font-family: Georgia, "Times New Roman", serif; font-size: 25px; font-weight: 600; letter-spacing: .12em; }
        .brand-wordmark-caption { margin-top: 2px; color: rgba(255,255,255,.64); font-size: 9px; font-weight: 600; letter-spacing: .19em; }

        .brand-content { width: min(100%, 520px); margin: auto 0; padding: 80px 0 72px; }
        .eyebrow, .access-label {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .18em;
            line-height: 1.5;
            text-transform: uppercase;
        }
        .eyebrow { color: #e8cbd1; }
        .eyebrow::before { width: 24px; height: 1px; background: #d8aeb8; content: ""; }
        .brand-title {
            max-width: 510px;
            margin: 24px 0 18px;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(43px, 5.3vw, 68px);
            font-weight: 400;
            letter-spacing: -.045em;
            line-height: 1.04;
        }
        .brand-description { max-width: 410px; margin: 0; color: rgba(255,255,255,.76); font-size: 15px; line-height: 1.8; }

        .workspace-card {
            position: relative;
            width: min(100%, 370px);
            margin: 50px 0 0 auto;
            padding: 19px 20px 17px;
            border: 1px solid rgba(255,255,255,.22);
            border-radius: 12px;
            background: rgba(255,255,255,.105);
            box-shadow: 0 18px 44px rgba(34, 3, 12, .2), inset 0 1px rgba(255,255,255,.12);
            backdrop-filter: blur(12px);
            animation-name: content-arrive, card-drift;
            animation-duration: 250ms, 7s;
            animation-delay: 190ms, 900ms;
            animation-timing-function: var(--ease-out), ease-in-out;
            animation-iteration-count: 1, infinite;
            animation-fill-mode: forwards, both;
        }
        .workspace-top { display: flex; align-items: center; justify-content: space-between; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,.16); }
        .workspace-label { display: flex; align-items: center; gap: 9px; font-size: 11px; font-weight: 650; letter-spacing: .015em; }
        .workspace-label svg { width: 17px; height: 17px; color: #f2dce1; }
        .live-dot { display: inline-flex; align-items: center; gap: 6px; color: #f3dfe3; font-size: 9px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        .live-dot::before { width: 6px; height: 6px; border-radius: 50%; background: #e7c1ca; box-shadow: 0 0 0 3px rgba(231,193,202,.13); content: ""; }
        .workspace-bottom { display: flex; align-items: end; justify-content: space-between; padding-top: 15px; }
        .workspace-caption { color: rgba(255,255,255,.63); font-size: 10px; }
        .workspace-value { margin-top: 5px; font-family: Georgia, "Times New Roman", serif; font-size: 17px; }
        .workspace-symbol { display: grid; width: 34px; height: 34px; place-items: center; border: 1px solid rgba(255,255,255,.18); border-radius: 9px; color: #f2dce1; }
        .workspace-symbol svg { width: 17px; height: 17px; }
        .brand-footer { color: rgba(255,255,255,.58); font-size: 10px; letter-spacing: .035em; }

        .form-panel {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: clamp(36px, 7vw, 96px) clamp(34px, 8vw, 112px);
            background: radial-gradient(ellipse at 100% 0%, rgba(245,228,232,.54), transparent 43%), var(--canvas);
        }
        .form-inner { width: min(100%, 422px); }
        .form-heading { margin-bottom: 35px; }
        .access-label { color: var(--wine); }
        .access-label::before { width: 7px; height: 7px; border-radius: 50%; background: var(--rose); content: ""; }
        .form-title { margin: 17px 0 10px; font-family: Georgia, "Times New Roman", serif; font-size: clamp(39px, 4vw, 52px); font-weight: 400; letter-spacing: -.045em; line-height: 1.08; }
        .form-description { margin: 0; color: var(--muted); font-size: 14px; line-height: 1.75; }

        .validation-summary {
            margin: 0 0 22px;
            padding: 13px 15px;
            border: 1px solid #e7c8ce;
            border-radius: 8px;
            background: #fbf0f2;
            color: var(--wine);
            font-size: 12px;
            line-height: 1.55;
            animation: error-arrive 230ms var(--ease-out) both;
        }
        .validation-summary ul { margin: 0; padding-left: 17px; }
        .validation-summary li + li { margin-top: 4px; }

        .field { margin-top: 20px; }
        .field-label { display: block; margin-bottom: 8px; color: #3b3033; font-size: 12px; font-weight: 650; letter-spacing: .01em; }
        .input-wrap { position: relative; }
        .field-icon { position: absolute; top: 50%; left: 15px; width: 17px; height: 17px; color: #9b858a; pointer-events: none; transform: translateY(-50%); }
        .text-input {
            display: block;
            width: 100%;
            height: 52px;
            padding: 0 15px 0 44px;
            border: 1px solid var(--line);
            border-radius: 8px;
            outline: none;
            background: rgba(255,255,255,.86);
            color: var(--ink);
            font-size: 13px;
            transition: border-color 170ms ease, box-shadow 170ms ease, background-color 170ms ease;
        }
        .text-input::placeholder { color: #a9999d; }
        .text-input:focus { border-color: var(--rose); background: var(--white); box-shadow: 0 0 0 3px rgba(177,141,148,.17); }
        .text-input[aria-invalid="true"] { border-color: #a64254; }
        .text-input[aria-invalid="true"]:focus { box-shadow: 0 0 0 3px rgba(166,66,84,.13); }
        .field-error { margin: 7px 0 0; color: #8d263a; font-size: 11px; line-height: 1.5; }
        .password-input { padding-right: 51px; }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 8px;
            display: grid;
            width: 36px;
            height: 36px;
            padding: 0;
            place-items: center;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #816d72;
            cursor: pointer;
            transform: translateY(-50%);
            transition: color 150ms ease, background-color 150ms ease, transform 150ms var(--ease-out);
        }
        .password-toggle:active { transform: translateY(-50%) scale(.94); }
        .password-toggle:focus-visible, .remember-control:focus-within { outline: 2px solid #9b6873; outline-offset: 3px; }
        .password-toggle svg { position: absolute; width: 17px; height: 17px; transition: opacity 130ms ease, transform 180ms var(--ease-out); }
        .password-toggle .icon-eye-off { opacity: 0; transform: scale(.88) rotate(-12deg); }
        .password-toggle[aria-pressed="true"] .icon-eye { opacity: 0; transform: scale(.88) rotate(12deg); }
        .password-toggle[aria-pressed="true"] .icon-eye-off { opacity: 1; transform: scale(1) rotate(0); }

        .form-options { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin: 18px 0 25px; }
        .remember-control { display: inline-flex; align-items: center; gap: 9px; color: #594d50; cursor: pointer; font-size: 12px; }
        .remember-control input { width: 15px; height: 15px; margin: 0; accent-color: var(--wine); cursor: pointer; }

        .submit-button {
            display: flex;
            width: 100%;
            min-height: 52px;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 1px solid var(--wine);
            border-radius: 8px;
            background: var(--wine);
            box-shadow: 0 7px 16px rgba(109,15,34,.16), inset 0 1px rgba(255,255,255,.16);
            color: var(--white);
            cursor: pointer;
            font-size: 13px;
            font-weight: 650;
            letter-spacing: .015em;
            transition: background-color 160ms ease, box-shadow 160ms ease, transform 150ms var(--ease-out);
        }
        .submit-button:active { transform: scale(.985); box-shadow: 0 3px 9px rgba(109,15,34,.16); }
        .submit-button:focus-visible { outline: 3px solid rgba(109,15,34,.27); outline-offset: 3px; }
        .submit-button svg { width: 16px; height: 16px; transition: transform 170ms var(--ease-out); }

        @media (hover: hover) and (pointer: fine) {
            .text-input:hover { border-color: #cfb8be; }
            .password-toggle:hover { background: #f7eff1; color: var(--wine); }
            .submit-button:hover { background: #7b192d; box-shadow: 0 9px 20px rgba(109,15,34,.2), inset 0 1px rgba(255,255,255,.16); }
            .submit-button:hover svg { transform: translateX(3px); }
        }

        .secure-note { display: flex; align-items: center; justify-content: center; gap: 8px; margin: 24px 0 0; color: #8a7a7e; font-size: 10px; letter-spacing: .02em; }
        .secure-note svg { width: 14px; height: 14px; color: #8b6870; }

        @keyframes shell-arrive { from { opacity: 0; transform: translateY(9px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes content-arrive { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes card-drift { 0%, 100% { translate: 0 0; } 50% { translate: 0 -5px; } }
        @keyframes error-arrive { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 980px) {
            .login-shell { grid-template-columns: minmax(330px, .88fr) minmax(420px, 1.12fr); }
            .brand-panel { padding-right: 36px; padding-left: 40px; }
            .form-panel { padding-right: 48px; padding-left: 48px; }
            .brand-title { font-size: clamp(42px, 5.4vw, 58px); }
        }
        @media (max-width: 760px) {
            .login-shell { display: flex; min-height: 100vh; flex-direction: column; }
            .brand-panel { min-height: auto; padding: 25px 28px 29px; }
            .brand-mark { width: 39px; height: 39px; border-radius: 11px; }
            .brand-mark svg { width: 23px; height: 23px; }
            .brand-name { font-size: 21px; }
            .brand-content { padding: 48px 0 8px; }
            .brand-title { max-width: 500px; margin-top: 17px; font-size: clamp(38px, 9vw, 54px); }
            .brand-description { max-width: 520px; font-size: 13px; }
            .workspace-card { display: none; }
            .brand-footer { margin-top: 36px; font-size: 9px; }
            .form-panel { min-height: auto; flex: 1; padding: 44px 28px 52px; background: radial-gradient(ellipse at 100% 0%, rgba(245,228,232,.58), transparent 46%), var(--canvas); }
            .form-inner { width: min(100%, 470px); }
            .form-heading { margin-bottom: 29px; }
            .form-title { font-size: 43px; }
        }
        @media (max-width: 420px) {
            .brand-panel { padding-right: 22px; padding-left: 22px; }
            .form-panel { padding-right: 22px; padding-left: 22px; }
            .form-options { margin-top: 16px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; }
            .login-shell, .brand-lockup, .brand-copy, .workspace-card, .brand-footer,
            .form-heading, .login-form, .secure-note, .validation-summary { animation: none !important; opacity: 1; transform: none; }
            .workspace-card { translate: none; }
            .password-toggle svg, .password-toggle, .submit-button, .submit-button svg, .text-input { transition-duration: 0.01ms; }
        }
    </style>
</head>
<body>
    <main class="login-shell" aria-label="SAMI administrative sign in">
        <section class="brand-panel" aria-labelledby="brand-heading">
            <a class="brand-lockup" href="{{ route('admin.login') }}" aria-label="SAMI home">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 32 32" fill="none">
                        <path d="M6 24V8h4.3l5.7 8.1L21.7 8H26v16h-4.2v-9.3l-5.8 8-5.8-8V24H6Z" fill="currentColor"/>
                        <path d="M5 27h22" stroke="currentColor" stroke-opacity=".58" stroke-width="1.2"/>
                    </svg>
                </span>
                <span><span class="brand-name">SAMI</span><span class="brand-wordmark-caption">CAMPUS SYSTEMS</span></span>
            </a>

            <div class="brand-content">
                <p class="eyebrow">Campus management platform</p>
                <h1 class="brand-title" id="brand-heading">One campus.<br>One connected experience.</h1>
                <p class="brand-description">A clearer view of campus life, with the people, services, and information your community relies on—connected in one place.</p>

                <aside class="workspace-card" aria-label="Administrative workspace">
                    <div class="workspace-top">
                        <div class="workspace-label">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="2.5" y="3" width="15" height="14" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M2.8 7h14.4M6.5 5h.1m2.8 0h.1" stroke="currentColor" stroke-linecap="round" stroke-width="1.4"/><path d="M6 10.5h3m-3 3h8" stroke="currentColor" stroke-linecap="round" stroke-width="1.3"/></svg>
                            Administrative workspace
                        </div>
                        <span class="live-dot">Secure</span>
                    </div>
                    <div class="workspace-bottom">
                        <div><div class="workspace-caption">Your campus, in sync</div><div class="workspace-value">Everything in one place.</div></div>
                        <span class="workspace-symbol" aria-hidden="true"><svg viewBox="0 0 20 20" fill="none"><path d="M4 14.5 8 10.5l2.6 2.6L16 7.7M12.5 7.7H16v3.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/></svg></span>
                    </div>
                </aside>
            </div>

            <footer class="brand-footer">© {{ date('Y') }} SAMI Campus Systems. All rights reserved.</footer>
        </section>

        <section class="form-panel" aria-labelledby="login-heading">
            <div class="form-inner">
                <header class="form-heading">
                    <p class="access-label">Administrator access</p>
                    <h2 class="form-title" id="login-heading">Welcome back.</h2>
                    <p class="form-description">Sign in using your authorized administrative account.</p>
                </header>

                @if ($errors->any())
                    <div class="validation-summary" role="alert" aria-live="polite">
                        Sign-in could not be completed. Review the highlighted field and try again.
                    </div>
                @endif

                <form class="login-form" method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <div class="field">
                        <label class="field-label" for="email">Email address</label>
                        <div class="input-wrap">
                            <svg class="field-icon" viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="2.5" y="4" width="15" height="12" rx="2" stroke="currentColor" stroke-width="1.35"/><path d="m3.5 5.5 6.5 5 6.5-5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.35"/></svg>
                            <input class="text-input" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="name@campus.edu" autocomplete="username" inputmode="email" required autofocus @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
                        </div>
                        @error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label class="field-label" for="password">Password</label>
                        <div class="input-wrap">
                            <svg class="field-icon" viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="3.25" y="8.5" width="13.5" height="9" rx="2" stroke="currentColor" stroke-width="1.35"/><path d="M6.25 8.5V6a3.75 3.75 0 0 1 7.5 0v2.5M10 12v2" stroke="currentColor" stroke-linecap="round" stroke-width="1.35"/></svg>
                            <input class="text-input password-input" id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required aria-describedby="@if ($errors->has('password'))password-error @endif password-toggle-hint" @if ($errors->has('password')) aria-invalid="true" @endif>
                            <button class="password-toggle" id="password-toggle" type="button" aria-label="Show password" aria-pressed="false" aria-controls="password">
                                <svg class="icon-eye" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M2.3 10s2.7-5 7.7-5 7.7 5 7.7 5-2.7 5-7.7 5-7.7-5-7.7-5Z" stroke="currentColor" stroke-width="1.4"/><circle cx="10" cy="10" r="2.2" stroke="currentColor" stroke-width="1.4"/></svg>
                                <svg class="icon-eye-off" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M3 3 17 17M8.6 5.2A8.7 8.7 0 0 1 10 5c5 0 7.7 5 7.7 5a11 11 0 0 1-2.2 2.7M5.1 5.9C3.3 7.2 2.3 10 2.3 10s2.7 5 7.7 5c.8 0 1.5-.1 2.2-.4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/><path d="M8.6 8.6a2 2 0 0 0 2.8 2.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.4"/></svg>
                            </button>
                        </div>
                        <span class="visually-hidden" id="password-toggle-hint">Use the show password button to reveal or hide your password.</span>
                        @error('password')<p class="field-error" id="password-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-options">
                        <label class="remember-control" for="remember">
                            <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                            <span>Remember me</span>
                        </label>
                    </div>

                    <button class="submit-button" type="submit">
                        <span>Sign in to SAMI</span>
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M3.5 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/></svg>
                    </button>
                </form>

                <p class="secure-note">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 2.5 16 5v4.2c0 3.7-2.5 6.7-6 8.3-3.5-1.6-6-4.6-6-8.3V5l6-2.5Z" stroke="currentColor" stroke-linejoin="round" stroke-width="1.35"/><path d="m7.5 9.8 1.7 1.7 3.5-3.7" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.35"/></svg>
                    Your sign-in is protected by secure session authentication.
                </p>
            </div>
        </section>
    </main>

    <style>.visually-hidden{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}</style>
    <script>
        (() => {
            const password = document.getElementById('password');
            const toggle = document.getElementById('password-toggle');

            toggle.addEventListener('click', () => {
                const shouldShow = password.type === 'password';
                password.type = shouldShow ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', String(shouldShow));
                toggle.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
            });
        })();
    </script>
</body>
</html>
