<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora del sistema</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Bitácora del sistema</h1>
      <a href="/dashboard" class="text-indigo-600 underline">Volver al dashboard</a>
    </div>

    <div class="bg-white rounded shadow p-4">
      <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3">
        <select class="border rounded p-2" name="id_usuario">
          <option value="">Todos los usuarios</option>
          @foreach ($usuarios as $u)
            <option value="{{ $u->id_usuario }}" @selected(($filters['id_usuario'] ?? '') == $u->id_usuario)>
              {{ $u->nombre }} ({{ $u->correo }})
            </option>
          @endforeach
        </select>
        <input class="border rounded p-2" name="accion" placeholder="Acción" value="{{ $filters['accion'] ?? '' }}">
        <input class="border rounded p-2" name="ip" placeholder="IP" value="{{ $filters['ip'] ?? '' }}">
        <input class="border rounded p-2" type="date" name="fecha_desde" value="{{ $filters['fecha_desde'] ?? '' }}">
        <input class="border rounded p-2" type="date" name="fecha_hasta" value="{{ $filters['fecha_hasta'] ?? '' }}">
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white rounded p-2">Filtrar</button>
      </form>
    </div>

    <div class="bg-white rounded shadow p-4 overflow-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-2 text-left">Fecha</th>
            <th class="p-2 text-left">Hora</th>
            <th class="p-2 text-left">Usuario</th>
            <th class="p-2 text-left">Acción</th>
            <th class="p-2 text-left">Detalle</th>
            <th class="p-2 text-left">IP</th>
          </tr>
        </thead>
        <tbody>
        @forelse ($registros as $r)
          <tr class="border-t">
            <td class="p-2">{{ $r->fecha }}</td>
            <td class="p-2">{{ $r->hora }}</td>
            <td class="p-2">{{ $r->usuario->nombre ?? '-' }} ({{ $r->usuario->correo ?? '' }})</td>
            <td class="p-2">{{ $r->accion }}</td>
            <td class="p-2">{{ $r->detalle }}</td>
            <td class="p-2">{{ $r->ip }}</td>
          </tr>
        @empty
          <tr><td class="p-4 text-gray-600" colspan="6">Sin resultados</td></tr>
        @endforelse
        </tbody>
      </table>
      <div class="mt-3">{{ $registros->links() }}</div>
    </div>
  </div>
</body>
</html>
