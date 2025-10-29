<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | Materias</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Materias</h1>
      <a href="/admin/usuarios" class="text-indigo-600 underline">Usuarios</a>
    </div>

    @if (session('success'))
      <div class="bg-green-100 text-green-800 border border-green-300 rounded p-3">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="bg-red-100 text-red-800 border border-red-300 rounded p-3">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white rounded shadow p-4">
      <h2 class="font-semibold mb-3">Crear materia</h2>
      <form method="POST" action="{{ route('admin.materias.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
        @csrf
        <input class="border rounded p-2" name="sigla" placeholder="Sigla" required>
        <input class="border rounded p-2 md:col-span-2" name="nombre" placeholder="Nombre" required>
        <input class="border rounded p-2" name="semestre" placeholder="Semestre">
        <input class="border rounded p-2" type="number" name="creditos" placeholder="Créditos">
        <input class="border rounded p-2" type="number" name="horas_semana" placeholder="Horas/semana">
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white rounded p-2">Crear</button>
      </form>
    </div>

    <div class="bg-white rounded shadow p-4">
      <h2 class="font-semibold mb-3">Listado</h2>
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-2 text-left">ID</th>
            <th class="p-2 text-left">Sigla</th>
            <th class="p-2 text-left">Nombre</th>
            <th class="p-2 text-left">Semestre</th>
            <th class="p-2 text-left">Créditos</th>
            <th class="p-2 text-left">Horas/Sem.</th>
            <!-- <th class="p-2 text-left">Acciones</th> -->
          </tr>
        </thead>
        <tbody>
        @foreach ($materias as $m)
          <tr class="border-t">
            <td class="p-2">{{ $m->id_materia }}</td>
            <td class="p-2">{{ $m->sigla }}</td>
            <td class="p-2">{{ $m->nombre }}</td>
            <td class="p-2">{{ $m->semestre }}</td>
            <td class="p-2">{{ $m->creditos }}</td>
            <td class="p-2">{{ $m->horas_semana }}</td>
            <!--
            <td class="p-2 space-x-2">
              [Edición/Eliminación deshabilitadas]
            </td>
            -->
          </tr>
        @endforeach
        </tbody>
      </table>
      <div class="mt-3">{{ $materias->links() }}</div>
    </div>
  </div>
</body>
</html>
