<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FormaPro') - Plateforme de formations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-indigo-700 text-white shadow">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-xl font-bold">FormaPro</a>
            <div class="space-x-6">
                <a href="{{ route('home') }}" class="hover:text-indigo-200">Accueil</a>
                <a href="{{ route('formations.index') }}" class="hover:text-indigo-200">Formations</a>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-6xl mx-auto px-6 py-10 w-full">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-gray-300 text-center py-4">
        <p class="text-sm">&copy; {{ date('Y') }} FormaPro &mdash; Mini-projet DevOps</p>
    </footer>
</body>
</html>
