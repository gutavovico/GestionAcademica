<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Docente</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Hola, {{ $user->nombre }}</h1>
      <form method="POST" action="/logout">@csrf <button class="bg-red-600 text-white rounded px-3 py-1.5">Cerrar sesión</button></form>
    </div>
    <div class="bg-white rounded shadow p-6">
      <p class="text-gray-700">Este es tu panel como Docente. Aquí irán accesos a asistencia, horarios, etc.</p>
    </div>
  </div>
</body>
</html>

