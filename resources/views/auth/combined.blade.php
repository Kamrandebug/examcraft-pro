<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $active === 'register' ? 'Create Account' : 'Sign In' }} — ExamCraft Pro</title>

  <!-- Google Fonts (same as landing page) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    /* ── Reset & Base ─────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      -webkit-font-smoothing: antialiased;
    }

    /* ── Page Background ──────────────────────────────── */
    .auth-page {
      min-height: 100vh;
      background: linear-gradient(145deg, #1B2A4A 0%, #243559 55%, #1a2847 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
      position: relative;
      overflow: hidden;
    }

    /* Dot-grid decorative overlay */
    .auth-grid-bg {
      position: fixed;
      inset: 0;
      background-image: radial-gradient(circle, rgba(201,168,76,0.07) 1px, transparent 1px);
      background-size: 30px 30px;
      pointer-events: none;
      z-index: 0;
    }

    /* Subtle ambient glow blobs */
    .auth-page::before,
    .auth-page::after {
      content: '';
      position: fixed;
      border-radius: 50%;
      pointer-events: none;
      z-index: 0;
    }
    .auth-page::before {
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(201,168,76,0.06) 0%, transparent 70%);
      top: -150px;
      right: -150px;
    }
    .auth-page::after {
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(27,42,74,0.8) 0%, transparent 70%);
      bottom: -100px;
      left: -100px;
    }

    /* ── Container ────────────────────────────────────── */
    .auth-container {
      position: relative;
      z-index: 1;
      max-width: 470px;
      width: 100%;
    }

    /* ── Brand Header ─────────────────────────────────── */
    .brand-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .brand-name {
      font-family: 'EB Garamond', Georgia, serif;
      font-size: 1.85rem;
      font-weight: 600;
      color: #C9A84C;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      line-height: 1;
      letter-spacing: -0.01em;
    }

    .brand-pro {
      color: #F7F2E4;
      opacity: 0.85;
    }

    .brand-tagline {
      font-size: 11.5px;
      color: rgba(247, 242, 228, 0.4);
      letter-spacing: 0.12em;
      text-transform: uppercase;
      margin-top: 6px;
      font-weight: 400;
    }

    /* ── Flip Scene (perspective wrapper) ─────────────── */
    .flip-scene {
      perspective: 1200px;
      width: 100%;
      /* Height synced by JS */
      transition: height 0.45s cubic-bezier(0.4, 0.2, 0.2, 1);
    }

    /* ── Flip Card ────────────────────────────────────── */
    .flip-card {
      position: relative;
      transform-style: preserve-3d;
      transition: transform 0.45s cubic-bezier(0.4, 0.2, 0.2, 1);
      width: 100%;
    }
    .flip-card.is-flipped {
      transform: rotateY(180deg);
    }

    /* ── Card Face (shared) ───────────────────────────── */
    .card-face {
      background: #FFFFFF;
      border-radius: 18px;
      padding: 2.5rem 2.25rem 2rem;
      box-shadow:
        0 32px 64px rgba(0, 0, 0, 0.45),
        0 0 0 1px rgba(201, 168, 76, 0.18),
        inset 0 1px 0 rgba(255,255,255,0.9);
      border-top: 3px solid #C9A84C;
      backface-visibility: hidden;
      -webkit-backface-visibility: hidden;
      width: 100%;
    }

    .card-back {
      transform: rotateY(180deg);
      position: absolute;
      top: 0;
      left: 0;
    }

    /* ── Card Typography ──────────────────────────────── */
    .card-title {
      font-family: 'EB Garamond', Georgia, serif;
      font-size: 2rem;
      font-weight: 600;
      color: #1B2A4A;
      line-height: 1.15;
      margin-bottom: 4px;
      letter-spacing: -0.01em;
    }

    .card-subtitle {
      font-size: 13px;
      color: #6b7280;
      margin-bottom: 1.75rem;
      font-weight: 400;
      line-height: 1.4;
    }

    /* ── Status Message ───────────────────────────────── */
    .status-msg {
      background: rgba(201, 168, 76, 0.1);
      border: 1px solid rgba(201, 168, 76, 0.35);
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 13px;
      color: #92400e;
      margin-bottom: 1.25rem;
      line-height: 1.4;
    }

    /* ── Form Fields ──────────────────────────────────── */
    .field-group {
      margin-bottom: 1.125rem;
    }

    .field-label {
      display: block;
      font-size: 12.5px;
      font-weight: 600;
      color: #1B2A4A;
      margin-bottom: 5px;
      letter-spacing: 0.025em;
    }

    .field-input {
      display: block;
      width: 100%;
      padding: 10px 13px;
      border: 1.5px solid rgba(27, 42, 74, 0.18);
      border-radius: 9px;
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      color: #1B2A4A;
      background: #FAFAF9;
      outline: none;
      transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
      -webkit-appearance: none;
    }

    .field-input::placeholder {
      color: #aab0bc;
      font-weight: 300;
    }

    .field-input:focus {
      border-color: #C9A84C;
      box-shadow: 0 0 0 3.5px rgba(201, 168, 76, 0.14);
      background: #FFFFFF;
    }

    .field-input--error {
      border-color: #dc2626;
      box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .field-error {
      display: block;
      font-size: 12px;
      color: #8B1A1A;
      margin-top: 5px;
      font-weight: 500;
    }

    /* ── Remember + Forgot row ────────────────────────── */
    .auth-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.25rem;
    }

    .remember-label {
      display: flex;
      align-items: center;
      gap: 7px;
      font-size: 13px;
      color: #374151;
      cursor: pointer;
      user-select: none;
    }

    .remember-check {
      width: 15px;
      height: 15px;
      accent-color: #C9A84C;
      cursor: pointer;
      border-radius: 4px;
    }

    .forgot-link {
      font-size: 12.5px;
      color: #C9A84C;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.15s;
    }
    .forgot-link:hover {
      color: #b8943e;
      text-decoration: underline;
      text-underline-offset: 2px;
    }

    /* ── Primary Button ───────────────────────────────── */
    .btn-primary {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 12px 20px;
      background: #C9A84C;
      color: #1B2A4A;
      border: none;
      border-radius: 9px;
      font-family: 'Inter', sans-serif;
      font-size: 13.5px;
      font-weight: 700;
      letter-spacing: 0.07em;
      text-transform: uppercase;
      cursor: pointer;
      transition: background 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
      box-shadow: 0 4px 14px rgba(201, 168, 76, 0.35);
      margin-top: 0.25rem;
    }
    .btn-primary:hover {
      background: #b8943e;
      box-shadow: 0 6px 20px rgba(201, 168, 76, 0.45);
    }
    .btn-primary:active {
      transform: scale(0.98);
      box-shadow: 0 2px 8px rgba(201, 168, 76, 0.3);
    }

    .btn-arrow {
      flex-shrink: 0;
      opacity: 0.8;
    }

    /* ── Card Divider ─────────────────────────────────── */
    .card-divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(27,42,74,0.1), transparent);
      margin: 1.5rem 0 1.25rem;
    }

    /* ── Switch Text + Flip Trigger ───────────────────── */
    .switch-text {
      text-align: center;
      font-size: 13px;
      color: #6b7280;
    }

    .flip-trigger {
      background: none;
      border: none;
      color: #C9A84C;
      font-family: 'Inter', sans-serif;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      padding: 0;
      text-decoration: underline;
      text-underline-offset: 2px;
      transition: color 0.15s;
    }
    .flip-trigger:hover {
      color: #b8943e;
    }

    /* ── Auth tagline (bottom) ────────────────────────── */
    .auth-tagline {
      text-align: center;
      color: rgba(247, 242, 228, 0.3);
      font-size: 11.5px;
      margin-top: 1.75rem;
      letter-spacing: 0.04em;
    }

    /* ── Responsive ───────────────────────────────────── */
    @media (max-width: 520px) {
      .auth-page { padding: 1.25rem 0.75rem; }
      .card-face  { padding: 2rem 1.5rem 1.75rem; }
      .card-title { font-size: 1.65rem; }
      .brand-name { font-size: 1.5rem; }
      .auth-row   { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
    }
  </style>
</head>
<body>

<div class="auth-page" id="authPage">

  <!-- Dot-grid decorative layer (pure CSS, pointer-events:none) -->
  <div class="auth-grid-bg" aria-hidden="true"></div>

  <div class="auth-container">

    <!-- ── Brand Header ─────────────────────────────────── -->
    <div class="brand-header">
      <div class="brand-name">
        <!-- ExamCraft square-grid SVG icon -->
        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <rect x="1"  y="1"  width="12" height="12" rx="2.5" fill="#C9A84C"/>
          <rect x="17" y="1"  width="12" height="12" rx="2.5" fill="rgba(201,168,76,0.45)"/>
          <rect x="1"  y="17" width="12" height="12" rx="2.5" fill="rgba(201,168,76,0.45)"/>
          <rect x="17" y="17" width="12" height="12" rx="2.5" fill="#C9A84C"/>
        </svg>
        ExamCraft <span class="brand-pro">Pro</span>
      </div>
      <p class="brand-tagline">Professional Exam Authoring</p>
    </div>

    <!-- ── Flip Scene ────────────────────────────────────── -->
    <div class="flip-scene" id="flipScene">
      <div class="flip-card{{ $active === 'register' ? ' is-flipped' : '' }}" id="flipCard">

        <!-- ════ FRONT — LOGIN ════ -->
        <div class="card-face card-front">

          <h2 class="card-title">Welcome back</h2>
          <p class="card-subtitle">Sign in to your ExamCraft account</p>

          <!-- Session Status -->
          @if (session('status'))
            <div class="status-msg">{{ session('status') }}</div>
          @endif

          <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <!-- Email -->
            <div class="field-group">
              <label class="field-label" for="login_email">Email address</label>
              <input
                class="field-input{{ $errors->has('email') && $active === 'login' ? ' field-input--error' : '' }}"
                id="login_email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="username"
                autofocus
                placeholder="you@school.edu"
              >
              @error('email')
                <span class="field-error">{{ $message }}</span>
              @enderror
            </div>

            <!-- Password -->
            <div class="field-group">
              <label class="field-label" for="login_password">Password</label>
              <div class="input-wrapper">
                <input
                  class="field-input{{ $errors->has('password') && $active === 'login' ? ' field-input--error' : '' }}"
                  id="login_password"
                  type="password"
                  name="password"
                  autocomplete="current-password"
                  placeholder="••••••••"
                >
              </div>
              @error('password')
                <span class="field-error">{{ $message }}</span>
              @enderror
            </div>

            <!-- Remember + Forgot -->
            <div class="auth-row">
              <label class="remember-label">
                <input type="checkbox" name="remember" class="remember-check"
                  {{ old('remember') ? 'checked' : '' }}>
                Remember me
              </label>
              @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
              @endif
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-primary">
              Log In
              <svg class="btn-arrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </form>

          <!-- Flip trigger -->
          <div class="card-divider"></div>
          <p class="switch-text">
            Don't have an account?
            <button type="button" class="flip-trigger" data-target="register">
              Create one free →
            </button>
          </p>

        </div><!-- /card-front -->

        <!-- ════ BACK — REGISTER ════ -->
        <div class="card-face card-back">

          <h2 class="card-title">Create your account</h2>
          <p class="card-subtitle">Start building professional exam papers</p>

          <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <!-- Name -->
            <div class="field-group">
              <label class="field-label" for="reg_name">Full name</label>
              <input
                class="field-input{{ $errors->has('name') ? ' field-input--error' : '' }}"
                id="reg_name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                autocomplete="name"
                placeholder="Your name"
              >
              @error('name')
                <span class="field-error">{{ $message }}</span>
              @enderror
            </div>

            <!-- Email -->
            <div class="field-group">
              <label class="field-label" for="reg_email">Email address</label>
              <input
                class="field-input{{ $errors->has('email') && $active === 'register' ? ' field-input--error' : '' }}"
                id="reg_email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="username"
                placeholder="you@school.edu"
              >
              @error('email')
                <span class="field-error">{{ $message }}</span>
              @enderror
            </div>

            <!-- Password -->
            <div class="field-group">
              <label class="field-label" for="reg_password">Password</label>
              <input
                class="field-input{{ $errors->has('password') && $active === 'register' ? ' field-input--error' : '' }}"
                id="reg_password"
                type="password"
                name="password"
                autocomplete="new-password"
                placeholder="Minimum 8 characters"
              >
              @error('password')
                <span class="field-error">{{ $message }}</span>
              @enderror
            </div>

            <!-- Confirm Password -->
            <div class="field-group">
              <label class="field-label" for="reg_password_confirm">Confirm password</label>
              <input
                class="field-input"
                id="reg_password_confirm"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="Repeat password"
              >
              @error('password_confirmation')
                <span class="field-error">{{ $message }}</span>
              @enderror
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-primary">
              Get Started Free
              <svg class="btn-arrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </form>

          <!-- Flip trigger -->
          <div class="card-divider"></div>
          <p class="switch-text">
            Already have an account?
            <button type="button" class="flip-trigger" data-target="login">
              ← Sign in
            </button>
          </p>

        </div><!-- /card-back -->

      </div><!-- /flip-card -->
    </div><!-- /flip-scene -->

    <!-- Bottom trust line -->
    <p class="auth-tagline">Trusted by educators teaching O Level · A Level · IGCSE</p>

  </div><!-- /auth-container -->
</div><!-- /auth-page -->

<script>
  (function () {
    'use strict';

    const scene    = document.getElementById('flipScene');
    const card     = document.getElementById('flipCard');
    const front    = card.querySelector('.card-front');
    const back     = card.querySelector('.card-back');
    const triggers = card.querySelectorAll('.flip-trigger');

    /* ── Height sync ───────────────────────────────── */
    function syncHeight() {
      const isFlipped = card.classList.contains('is-flipped');
      // Temporarily remove absolute positioning to measure true height
      back.style.position = 'relative';
      back.style.visibility = 'hidden';
      const frontH = front.scrollHeight;
      const backH  = back.scrollHeight;
      back.style.position  = '';
      back.style.visibility = '';

      const target = isFlipped ? backH : frontH;
      scene.style.height = target + 'px';
      // Also size the card itself so the absolute back face has a reference
      card.style.height = Math.max(frontH, backH) + 'px';
    }

    /* ── Flip ──────────────────────────────────────── */
    triggers.forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        card.classList.toggle('is-flipped');
        syncHeight();

        // After transition, remove explicit height so the card breathes
        setTimeout(function () {
          syncHeight(); // re-measure after DOM settles
        }, 470);
      });
    });

    /* ── Init ──────────────────────────────────────── */
    // Make back face absolute so only front contributes to natural flow height
    back.style.position = 'absolute';
    back.style.top      = '0';
    back.style.left     = '0';
    back.style.width    = '100%';

    syncHeight();
    window.addEventListener('resize', syncHeight);

    /* ── Auto-focus correct face on load ───────────── */
    const isFlippedOnLoad = card.classList.contains('is-flipped');
    if (isFlippedOnLoad) {
      const firstInput = back.querySelector('input');
      if (firstInput) setTimeout(function () { firstInput.focus(); }, 700);
    } else {
      const firstInput = front.querySelector('input');
      if (firstInput) firstInput.focus();
    }
  })();
</script>

</body>
</html>
