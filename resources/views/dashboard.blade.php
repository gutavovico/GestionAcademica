<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-4">
                    <span class="text-xl font-bold text-indigo-700">Gestión Académica</span>
                    <a href="/test" class="text-sm text-gray-600 hover:text-indigo-600">Test</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ $user->nombre ?? 'Usuario' }}</span>
                    <form method="POST" action="/logout">
                        @csrf
                        <button class="bg-red-600 text-white px-3 py-1.5 rounded text-sm">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-semibold mb-2">Bienvenido(a), {{ $user->nombre ?? 'Usuario' }}</h1>
            <p class="text-gray-600 mb-4">Has iniciado sesión correctamente.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-indigo-50 rounded">
                    <div class="text-xs uppercase text-indigo-700">Correo</div>
                    <div class="text-gray-800">{{ $user->correo ?? '-' }}</div>
                </div>
                <div class="p-4 bg-indigo-50 rounded">
                    <div class="text-xs uppercase text-indigo-700">Teléfono</div>
                    <div class="text-gray-800">{{ $user->telefono ?? '-' }}</div>
                </div>
                <div class="p-4 bg-indigo-50 rounded">
                    <div class="text-xs uppercase text-indigo-700">Rol</div>
                    <div class="text-gray-800">{{ $user->id_rol ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-6">
                <a href="/" class="inline-block text-indigo-600 hover:text-indigo-800 underline">Ir a inicio</a>
            </div>
        </div>
    </main>
</body>
</html>

