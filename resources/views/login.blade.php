<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <!-- Incluye Tailwind CSS o tus estilos preferidos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md">
        <div class="bg-white shadow-xl rounded-lg p-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Iniciar Sesión</h2>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error</strong>
                    <span class="block sm:inline"> {{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="/login">
                @csrf

                <div class="mb-5">
                    <label for="correo" class="block text-gray-700 text-sm font-semibold mb-2">Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" value="{{ old('correo') }}" required autofocus
                           class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('correo') border-red-500 @enderror">
                    @error('correo')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="contrasena" class="block text-gray-700 text-sm font-semibold mb-2">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" required
                           class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('contrasena') border-red-500 @enderror">
                    @error('contrasena')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        Ingresar
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

