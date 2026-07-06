<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $reportCard->student->full_name }}</title>
</head>
<body>
    @include('report-cards.partials.card')
</body>
</html>
