<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | Roles</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Roles</h1>
      <a href="/admin/usuarios" class="text-indigo-600 underline">Gestionar usuarios</a>
    </div>

    @if (session('success'))
      <div class="bg-green-100 text-green-800 border border-green-300 rounded p-3">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="bg-red-100 text-red-800 border border-red-300 rounded p-3">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white rounded shadow p-4">
      <h2 class="font-semibold mb-3">Crear rol</h2>
      <form method="POST" action="{{ route('admin.roles.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
        @csrf
        <input class="border rounded p-2 md:col-span-2" name="nombre" placeholder="Nombre" required>
        <input class="border rounded p-2 md:col-span-3" name="descripcion" placeholder="Descripción">
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white rounded p-2 md:col-span-1">Crear</button>
      </form>
    </div>

    <div class="bg-white rounded shadow p-4">
      <h2 class="font-semibold mb-3">Listado</h2>
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-2 text-left">ID</th>
            <th class="p-2 text-left">Nombre</th>
            <th class="p-2 text-left">Descripción</th>
            <th class="p-2 text-left">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($roles as $r)
            <tr class="border-t">
              <td class="p-2">{{ $r->id_rol }}</td>
              <td class="p-2">{{ $r->nombre }}</td>
              <td class="p-2">{{ $r->descripcion }}</td>
              <td class="p-2 space-x-2">
                <form method="POST" action="{{ route('admin.roles.update', $r->id_rol) }}" class="inline">
                  @csrf
                  <input class="border rounded p-1" name="nombre" value="{{ $r->nombre }}" placeholder="Nombre">
                  <input class="border rounded p-1" name="descripcion" value="{{ $r->descripcion }}" placeholder="Descripción">
                  <button class="bg-gray-800 text-white rounded px-2 py-1">Guardar</button>
                </form>
                <form method="POST" action="{{ route('admin.roles.destroy', $r->id_rol) }}" class="inline" onsubmit="return confirm('¿Eliminar rol?');">
                  @csrf
                  <button class="bg-red-600 text-white rounded px-2 py-1">Eliminar</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
