<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Idea</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background text-foreground">

    <x-layout.nav />
    <main class="min-h-[calc(100vh-4rem)] max-w-6xl mx-auto">
        {{ $slot }}
    </main>
</body>

</html>