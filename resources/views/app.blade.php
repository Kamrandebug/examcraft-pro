<!DOCTYPE html>
<html lang="en" data-theme="latenight">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>ExamCraft Pro v36</title>

<!-- Google Fonts — EXACT same link as v36.html -->
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400;1,600&family=EB+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400&family=Palatino+Linotype:ital,wght@0,400;0,700;1,400&family=Source+Serif+4:ital,opsz,wght@0,8..60,300;0,8..60,400;0,8..60,600;1,8..60,400&family=Noto+Serif:ital,wght@0,400;0,700;1,400&family=PT+Serif:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Georgia&display=swap" rel="stylesheet"/>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

<!-- Font Awesome 6.5 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<!-- QRCode.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

@vite(['resources/css/app.css', 'resources/js/app.js'])

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.authUser = @json([
            'name' => auth()->user()->name ?? 'User',
            'email' => auth()->user()->email ?? '',
            'role' => auth()->user()->role?->name ?? 'user'
        ]);
    </script>
</head>
<body>
<div id="app"></div>
</body>
</html>
