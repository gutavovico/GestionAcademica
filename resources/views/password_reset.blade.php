<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="w-full max-w-md">
    <div class="bg-white shadow rounded p-6">
      <h1 class="text-xl font-semibold mb-4">Nueva contraseña</h1>

      @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label class="block text-sm mb-1" for="correo">Correo</label>
        <input class="border rounded w-full p-2 mb-4" type="email" id="correo" name="correo" value="{{ old('correo', request('email')) }}" required>

        <label class="block text-sm mb-1" for="password">Nueva contraseña</label>
        <input class="border rounded w-full p-2 mb-4" type="password" id="password" name="password" required>

        <label class="block text-sm mb-1" for="password_confirmation">Confirmar contraseña</label>
        <input class="border rounded w-full p-2 mb-4" type="password" id="password_confirmation" name="password_confirmation" required>

        <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded">Guardar</button>
      </form>

      <div class="mt-4 text-sm">
        <a class="text-indigo-600 underline" href="{{ route('login') }}">Volver al inicio de sesion</a>
      </div>
    </div>
  </div>
</body>
</html>
