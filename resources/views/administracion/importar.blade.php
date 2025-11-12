<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Importar datos masivos de Excel/CSV</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <header class="max-w-5xl mx-auto mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Importar datos masivos de Excel/CSV</h1>
      <p class="text-slate-600">Cargar docentes, materias o grupos</p>
    </div>
    <a href="/dashboard" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">Volver al dashboard</a>
  </header>

  <main class="max-w-5xl mx-auto space-y-6">
    <section class="bg-white rounded-2xl shadow p-6 border border-slate-200">
      <h2 class="font-semibold text-slate-900 mb-4">Subir archivo</h2>
      @if ($errors->any())
        <div class="bg-rose-100 text-rose-800 border border-rose-300 rounded p-3 mb-3">{{ $errors->first() }}</div>
      @endif
      @if (session('resultado'))
        @php($r = session('resultado'))
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 rounded p-3 mb-3">
          Proceso finalizado — OK: {{ $r['ok'] ?? 0 }} / Total: {{ $r['total'] ?? 0 }}
        </div>
        @if (!empty($r['errores']))
          <div class="bg-amber-50 text-amber-800 border border-amber-200 rounded p-3 mb-3">
            <div class="font-medium">Errores:</div>
            <ul class="list-disc pl-5">
              @foreach($r['errores'] as $e)
                <li>Fila {{ $e['linea'] ?? '?' }}: {{ $e['error'] ?? 'Error' }}</li>
              @endforeach
            </ul>
          </div>
        @endif
      @endif

      <form action="{{ route('admin.importar.cargar') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
        @csrf
        <label class="block md:col-span-2">
          <span class="text-sm text-slate-700">Entidad</span>
          <select name="entidad" class="border rounded-lg p-2 w-full">
            <option value="usuarios" @selected(old('entidad')==='usuarios' || session('entidad')==='usuarios')>Usuarios (docentes)</option>
            <option value="materias" @selected(old('entidad')==='materias' || session('entidad')==='materias')>Materias</option>
            <option value="grupos" @selected(old('entidad')==='grupos' || session('entidad')==='grupos')>Grupos</option>
          </select>
        </label>
        <label class="block md:col-span-3">
          <span class="text-sm text-slate-700">Archivo CSV</span>
          <input type="file" name="archivo" accept=".csv,text/csv" class="border rounded-lg p-2 w-full bg-white" required />
        </label>
        <div class="flex gap-2">
          <button class="bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Importar</button>
        </div>
      </form>
    </section>

    <section class="bg-white rounded-2xl shadow p-6 border border-slate-200">
      <h2 class="font-semibold text-slate-900 mb-3">Plantillas (CSV)</h2>
      <p class="text-slate-600 mb-3">Descarga un archivo de ejemplo para cada entidad. Acepta separador coma o punto y coma. Codificación UTF-8 o ANSI.</p>
      <ul class="list-disc pl-5 space-y-1">
        <li><a class="text-cyan-700 hover:underline" href="/samples/usuarios.csv">usuarios.csv</a> — columnas: nombre,correo,telefono,contrasena,rol</li>
        <li><a class="text-cyan-700 hover:underline" href="/samples/materias.csv">materias.csv</a> — columnas: sigla,nombre,semestre,creditos,horas_semana</li>
        <li><a class="text-cyan-700 hover:underline" href="/samples/grupos.csv">grupos.csv</a> — columnas: nombre,turno,capacidad_max</li>
      </ul>
    </section>
  </main>
</body>
</html>
