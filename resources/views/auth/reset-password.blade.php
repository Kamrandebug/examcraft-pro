<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — ExamCraft Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f1b2d;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .brand {
            text-align: center;
            margin-bottom: 24px;
        }
        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #c9a84c, #e8c96d);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            color: #0f1b2d;
        }
        .brand-name {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
        }
        .brand-name span { color: #c9a84c; }
        .brand-sub {
            font-size: 11px;
            letter-spacing: 2px;
            color: #8899aa;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        }
        .card h2 {
            font-size: 24px;
            font-weight: 700;
            color: #0f1b2d;
            margin-bottom: 8px;
        }
        .card p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 28px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus { border-color: #c9a84c; }
        .error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 4px;
        }
        .btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #c9a84c, #e8c96d);
            color: #0f1b2d;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: opacity 0.2s;
            margin-top: 8px;
        }
        .btn:hover { opacity: 0.9; }
        .back-link {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #6b7280;
        }
        .back-link a { color: #c9a84c; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div>
        <div class="brand">
            <a href="/" class="brand-logo">
                <div class="brand-icon">E</div>
                <div>
                    <div class="brand-name">Exam<span>Craft</span> Pro</div>
                </div>
            </a>
            <div class="brand-sub">Professional Exam Authoring</div>
        </div>

        <div class="card">
            <h2>Set New Password</h2>
            <p>Enter your email and choose a strong new password.</p>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
                    @error('email') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" required>
                    @error('password') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn">Reset Password →</button>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">← Back to sign in</a>
            </div>
        </div>
    </div>
</body>
</html>