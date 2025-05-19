<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weathner</title>
</head>
<body>
<h1>Привіт!</h1>
<p>Ось погода для {{ $city }}</p>
<br>
<p>Температура: {{ $temperature }} градусів Цельсія</p>
<p>Вологість: {{ $humidity }}%</p>
<p>{{ $description }}</p>
<br>
<p>Гарного нстрою,<br>{{ config('app.name') }}</p>
</body>
</html>
