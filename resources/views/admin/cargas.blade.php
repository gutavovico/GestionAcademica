<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | Carga Horaria</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Asignar Carga Horaria</h1>
      <a href="/admin/usuarios" class="text-indigo-600 underline">Usuarios</a>
    </div>

    @if (session('success'))
      <div class="bg-green-100 text-green-800 border border-green-300 rounded p-3">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="bg-red-100 text-red-800 border border-red-300 rounded p-3">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white rounded shadow p-4">
      <h2 class="font-semibold mb-3">Nueva asignación</h2>
      <form method="POST" action="{{ route('admin.cargas.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
        @csrf
        <select class="border rounded p-2 md:col-span-2" name="id_usuario" required>
          <option value="">Seleccione Docente</option>
          @foreach ($docentes as $d)
            <option value="{{ $d->id_usuario }}">{{ $d->nombre }} ({{ $d->correo }})</option>
          @endforeach
        </select>
        <select class="border rounded p-2 md:col-span-2" name="id_materia" required>
          <option value="">Seleccione Materia</option>
          @foreach ($materias as $m)
            <option value="{{ $m->id_materia }}">{{ $m->sigla }} - {{ $m->nombre }}</option>
          @endforeach
        </select>
        <select class="border rounded p-2 md:col-span-2" name="id_grupo" required>
          <option value="">Seleccione Grupo</option>
          @foreach ($grupos as $g)
            <option value="{{ $g->id_grupo }}">{{ $g->nombre }} ({{ $g->turno }})</option>
          @endforeach
        </select>
        <input class="border rounded p-2" type="number" name="horas_semanales" placeholder="Horas/semana">
        <input class="border rounded p-2" name="gestion" placeholder="Gestión (p.ej. 2025-I)" required>
        <select class="border rounded p-2" name="estado">
          <option value="Activa">Activa</option>
          <option value="Inactiva">Inactiva</option>
        </select>
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white rounded p-2">Asignar</button>
      </form>
    </div>

    <div class="bg-white rounded shadow p-4">
      <h2 class="font-semibold mb-3">Asignaciones</h2>
      <div class="overflow-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="p-2 text-left">ID</th>
              <th class="p-2 text-left">Docente</th>
              <th class="p-2 text-left">Materia</th>
              <th class="p-2 text-left">Grupo</th>
              <th class="p-2 text-left">Horas</th>
              <th class="p-2 text-left">Gestión</th>
              <th class="p-2 text-left">Estado</th>
              <!-- <th class="p-2 text-left">Acciones</th> -->
            </tr>
          </thead>
          <tbody>
          @foreach ($cargas as $c)
            <tr class="border-t">
              <td class="p-2">{{ $c->id_carga }}</td>
              <td class="p-2">{{ $c->docente->nombre ?? '-' }}</td>
              <td class="p-2">{{ $c->materia->sigla ?? '-' }} - {{ $c->materia->nombre ?? '' }}</td>
              <td class="p-2">{{ $c->grupo->nombre ?? '-' }}</td>
              <td class="p-2">{{ $c->horas_semanales }}</td>
              <td class="p-2">{{ $c->gestion }}</td>
              <td class="p-2">{{ $c->estado }}</td>
              <!-- <td class="p-2">[Eliminación deshabilitada]</td> -->
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $cargas->links() }}</div>
    </div>
  </div>
</body>
</html>
