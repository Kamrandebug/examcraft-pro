<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Forgot Password — ExamCraft Pro</title>

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

    /* ── Card ─────────────────────────────────────────── */
    .card-face {
      background: #FFFFFF;
      border-radius: 18px;
      padding: 2.5rem 2.25rem 2rem;
      box-shadow:
        0 32px 64px rgba(0, 0, 0, 0.45),
        0 0 0 1px rgba(201, 168, 76, 0.18),
        inset 0 1px 0 rgba(255,255,255,0.9);
      border-top: 3px solid #C9A84C;
      width: 100%;
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

    /* ── Back link ────────────────────────────────────── */
    .back-link {
      display: block;
      text-align: center;
      margin-top: 1.25rem;
      font-size: 13px;
      color: #C9A84C;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.15s;
    }
    .back-link:hover {
      color: #b8943e;
      text-decoration: underline;
      text-underline-offset: 2px;
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

    <!-- ── Card ─────────────────────────────────────────── -->
    <div class="card-face">

      <h2 class="card-title">Forgot your password?</h2>
      <p class="card-subtitle">Enter your email address and we'll send you a link to reset it.</p>

      <!-- Session Status -->
      @if (session('status'))
        <div class="status-msg">{{ session('status') }}</div>
      @endif

      <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <!-- Email -->
        <div class="field-group">
          <label class="field-label" for="email">Email address</label>
          <input
            class="field-input{{ $errors->has('email') ? ' field-input--error' : '' }}"
            id="email"
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

        <!-- Submit -->
        <button type="submit" class="btn-primary">
          Email Password Reset Link
          <svg class="btn-arrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </form>

      <!-- Back to sign in -->
      <a href="{{ route('login') }}" class="back-link">← Back to sign in</a>

    </div><!-- /card-face -->

    <!-- Bottom trust line -->
    <p class="auth-tagline">Trusted by educators teaching O Level · A Level · IGCSE</p>

  </div><!-- /auth-container -->
</div><!-- /auth-page -->

</body>
</html>
