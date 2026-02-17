<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <p>Redirecting to patient management...</p>
    <script>window.location.href = '{{ route('patients.index') }}';</script>
</body>
</html>
