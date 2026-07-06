<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verified Report Card - {{ $reportCard->student->full_name }}</title>
    <style>
        body{margin:0;padding:24px;background:#eef3f0;font-family:Arial,sans-serif}
        .verified-banner{max-width:1100px;margin:0 auto 12px;padding:12px 18px;background:#d1e7dd;color:#0f5132;border:1px solid #badbcc;text-align:center;border-radius:6px}
        @media print{body{padding:0;background:#fff}.verified-banner{display:none}}
    </style>
</head>
<body>
    <div class="verified-banner">
        <strong>Verified school result</strong><br>
        This report card was published by the school and matches verification code {{ $reportCard->verification_code }}.
    </div>
    @include('report-cards.partials.card')
</body>
</html>
