<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Bienvenido, {{ $user->nombre }}</h1>
      <form method="POST" action="/logout">@csrf <button class="bg-red-600 text-white rounded px-3 py-1.5">Cerrar sesión</button></form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <a href="/admin/usuarios" class="block bg-white p-6 rounded shadow hover:shadow-md">
        <div class="text-lg font-semibold mb-2">Gestionar usuarios</div>
        <div class="text-gray-600">Crear, editar, activar/inactivar y asignar roles.</div>
      </a>
      <a href="/admin/roles" class="block bg-white p-6 rounded shadow hover:shadow-md">
        <div class="text-lg font-semibold mb-2">Gestionar roles</div>
        <div class="text-gray-600">Crear y editar roles del sistema.</div>
      </a>
      <a href="/test" class="block bg-white p-6 rounded shadow hover:shadow-md">
        <div class="text-lg font-semibold mb-2">Página de prueba</div>
        <div class="text-gray-600">Ver estado de sesión.</div>
      </a>
    </div>
  </div>
</body>
</html>

