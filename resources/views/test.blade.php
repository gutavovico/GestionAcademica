<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba Auth</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6 space-y-4">
        <h1 class="text-2xl font-bold">Prueba de Autenticación</h1>

        @auth
            <p>Sesión iniciada como: <strong>{{ auth()->user()->nombre ?? auth()->user()->email }}</strong></p>
            <form method="POST" action="/logout" class="mt-4">
                @csrf
                <button class="bg-red-600 text-white px-4 py-2 rounded">Cerrar sesión</button>
            </form>
            <a href="/dashboard" class="inline-block mt-2 text-indigo-600 underline">Ir a dashboard</a>
        @else
            <p>No has iniciado sesión.</p>
            <a href="/login" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded">Ir al login</a>
        @endauth
    </div>
</body>
</html>

