<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { margin:0; padding:0; background:#f3f4f6; font-family: 'Helvetica Neue', Arial, sans-serif; }
    .wrapper { padding: 40px 20px; }
    .container { max-width: 560px; margin: 0 auto; }
    .header {
        background: linear-gradient(135deg, #0f1b2d, #1a2d4a);
        border-radius: 12px 12px 0 0;
        padding: 32px;
        text-align: center;
    }
    .brand {
        display: inline-block;
        background: linear-gradient(135deg, #c9a84c, #e8c96d);
        color: #0f1b2d;
        font-size: 22px;
        font-weight: 800;
        padding: 8px 20px;
        border-radius: 8px;
        letter-spacing: 1px;
    }
    .tagline {
        color: #8899aa;
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-top: 8px;
    }
    .body {
        background: #ffffff;
        padding: 36px 40px;
    }
    .greeting {
        font-size: 20px;
        font-weight: 700;
        color: #0f1b2d;
        margin-bottom: 16px;
    }
    .content {
        font-size: 15px;
        color: #4b5563;
        line-height: 1.7;
        margin-bottom: 28px;
    }
    .btn-wrap { text-align: center; margin-bottom: 28px; }
    .btn {
        display: inline-block;
        padding: 14px 36px;
        background: linear-gradient(135deg, #c9a84c, #e8c96d);
        color: #0f1b2d !important;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-decoration: none;
        border-radius: 8px;
    }
    .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
    .url-fallback { font-size: 12px; color: #9ca3af; word-break: break-all; }
    .footer {
        background: #f9fafb;
        border-radius: 0 0 12px 12px;
        padding: 24px 40px;
        text-align: center;
        font-size: 12px;
        color: #9ca3af;
        border-top: 1px solid #e5e7eb;
    }
</style>
</head>
<body>
<div class="wrapper">
<div class="container">

    <div class="header">
        <div class="brand">ExamCraft Pro</div>
        <div class="tagline">Professional Exam Authoring</div>
    </div>

    <div class="body">
        <div class="greeting">
            @if(! empty($greeting)) {{ $greeting }} @else Hello! @endif
        </div>

        <div class="content">
            @foreach ($introLines as $line)
                <p>{{ $line }}</p>
            @endforeach
        </div>

        @isset($actionText)
        <div class="btn-wrap">
            <a href="{{ $actionUrl }}" class="btn">{{ $actionText }} →</a>
        </div>
        @endisset

        <div class="content">
            @foreach ($outroLines as $line)
                <p>{{ $line }}</p>
            @endforeach
        </div>

        @isset($actionText)
        <hr class="divider">
        <p class="url-fallback">
            If the button doesn't work, copy and paste this link into your browser:<br>
            <a href="{{ $actionUrl }}" style="color:#c9a84c;">{{ $actionUrl }}</a>
        </p>
        @endisset
    </div>

    <div class="footer">
        © {{ date('Y') }} ExamCraft Pro. All rights reserved.<br>
        If you did not request this email, no action is needed.
    </div>

</div>
</div>
</body>
</html>