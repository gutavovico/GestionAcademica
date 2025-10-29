<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | Usuarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Usuarios</h1>
      <a href="/admin/roles" class="text-indigo-600 underline">Gestionar roles</a>
    </div>

    @if (session('success'))
      <div class="bg-green-100 text-green-800 border border-green-300 rounded p-3">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="bg-red-100 text-red-800 border border-red-300 rounded p-3">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white rounded shadow p-4">
      <h2 class="font-semibold mb-3">Crear usuario</h2>
      <form method="POST" action="{{ route('admin.usuarios.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
        @csrf
        <input class="border rounded p-2 md:col-span-2" name="nombre" placeholder="Nombre" required>
        <input class="border rounded p-2 md:col-span-2" type="email" name="correo" placeholder="Correo" required>
        <input class="border rounded p-2 md:col-span-2" name="telefono" placeholder="Teléfono">
        <input class="border rounded p-2 md:col-span-2" type="password" name="contrasena" placeholder="Contraseña (min 8)" required>
        <select class="border rounded p-2 md:col-span-2" name="id_rol" required>
          <option value="">Seleccione rol</option>
          @foreach ($roles as $rol)
            <option value="{{ $rol->id_rol }}">{{ $rol->nombre }}</option>
          @endforeach
        </select>
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white rounded p-2 md:col-span-2">Crear</button>
      </form>
    </div>

    <div class="bg-white rounded shadow p-4">
      <h2 class="font-semibold mb-3">Listado</h2>
      <div class="overflow-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="p-2 text-left">ID</th>
              <th class="p-2 text-left">Nombre</th>
              <th class="p-2 text-left">Correo</th>
              <th class="p-2 text-left">Rol</th>
              <th class="p-2 text-left">Estado</th>
              <th class="p-2 text-left">Acciones</th>
            </tr>
          </thead>
          <tbody>
          @foreach ($usuarios as $u)
            <tr class="border-t">
              <td class="p-2">{{ $u->id_usuario }}</td>
              <td class="p-2">{{ $u->nombre }}</td>
              <td class="p-2">{{ $u->correo }}</td>
              <td class="p-2">{{ $u->rol->nombre ?? '-' }}</td>
              <td class="p-2">{{ ($u->estado ?? true) ? 'Activo' : 'Inactivo' }}</td>
              <td class="p-2 space-x-2">
                <form method="POST" action="{{ route('admin.usuarios.update', $u->id_usuario) }}" class="inline">
                  @csrf
                  <input class="border rounded p-1" name="nombre" value="{{ $u->nombre }}" placeholder="Nombre">
                  <input class="border rounded p-1" type="email" name="correo" value="{{ $u->correo }}" placeholder="Correo">
                  <select class="border rounded p-1" name="id_rol">
                    @foreach ($roles as $rol)
                      <option value="{{ $rol->id_rol }}" @selected($u->id_rol==$rol->id_rol)>{{ $rol->nombre }}</option>
                    @endforeach
                  </select>
                  <input class="border rounded p-1" name="telefono" value="{{ $u->telefono }}" placeholder="Teléfono">
                  <input class="border rounded p-1" type="password" name="contrasena" placeholder="Nueva contraseña (opcional)">
                  <button class="bg-gray-800 text-white rounded px-2 py-1">Guardar</button>
                </form>
                <form method="POST" action="{{ route('admin.usuarios.toggle', $u->id_usuario) }}" class="inline">
                  @csrf
                  <button class="bg-yellow-600 text-white rounded px-2 py-1">{{ ($u->estado ?? true) ? 'Desactivar' : 'Activar' }}</button>
                </form>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-3">{{ $usuarios->links() }}</div>
    </div>
  </div>
</body>
</html>

