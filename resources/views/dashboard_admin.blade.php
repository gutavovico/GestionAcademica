<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Perfil Administrativo</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eaf3ff] to-[#e3efff]">
  <header class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Perfil Administrativo</h1>
      <p class="text-slate-600">Panel de administracion</p>
    </div>
    <form method="POST" action="/logout">
      @csrf
      <button class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M15 12H3m12 0l-4-4m4 4l-4 4M21 4v16"/></svg>
        Cerrar Sesion
      </button>
    </form>
  </header>

  <main class="max-w-7xl mx-auto px-6 pb-10 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Columna izquierda -->
    <section class="lg:col-span-1 space-y-6">
      <!-- Informacion personal -->
      <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Informacion Personal</h2>
        <div class="text-slate-800 text-lg font-semibold">{{ $user->nombre ?? 'Administrador' }}</div>
        <div class="text-slate-500 mb-4">Administrador</div>
        <div class="space-y-2 text-sm text-slate-700">
          <div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M4 4h16v16H4z"/><path stroke-width="2" d="M22 6l-10 7L2 6"/></svg> {{ $user->correo ?? '-' }}</div>
          <div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M22 16.92a10 10 0 01-9.19 5.58 10 10 0 01-8.49-4.69L3 15a16 16 0 007 7l2.81-1.32A10 10 0 0022 16.92z"/></svg> {{ $user->telefono ?? '-' }}</div>
        </div>
      </div>

      <!-- Menu de opciones -->
      <div class="bg-white rounded-2xl shadow p-4">
        <h2 class="font-semibold text-slate-900 mb-3">Opciones</h2>
        <nav class="space-y-2 text-slate-700">
          <details open class="group">
            <summary class="flex items-center justify-between cursor-pointer bg-sky-50 text-sky-800 px-3 py-2 rounded-lg border border-sky-100">
              <span class="flex items-center gap-2"><span></span> Autenticacion y Seguridad</span>
              <span>></span>
            </summary>
            <div class="pl-4 py-2 space-y-1">
              <a href="/admin/usuarios" class="block hover:underline">Gestionar usuarios</a>
              <a href="/admin/roles" class="block hover:underline">Gestionar roles</a>
            </div>
          </details>

          <details class="group">
            <summary class="flex items-center justify-between cursor-pointer px-3 py-2 rounded-lg hover:bg-slate-50">
              <span class="flex items-center gap-2"><span></span> Gestion Academica</span>
              <span>></span>
            </summary>
            <div class="pl-4 py-2 space-y-1">
              <a href="/admin/materias" class="block hover:underline">Registrar materias</a>
              <a href="/admin/cargas" class="block hover:underline">Asignar carga horaria al docente</a>
              <span class="block text-slate-400">Importar datos a Excel/CSV (proximamente)</span>
            </div>
          </details>

          <details class="group">
            <summary class="flex items-center justify-between cursor-pointer px-3 py-2 rounded-lg hover:bg-slate-50">
              <span class="flex items-center gap-2"><span></span> Gestion de Aulas y Horarios</span>
              <span>></span>
            </summary>
            <div class="pl-4 py-2 space-y-1">
              <a href="/gestion-aulas/registrar" class="block hover:underline">Registrar aulas y capacidad</a>
              <a href="/gestion-aulas/registrar-aula-horario" class="block hover:underline">Registrar aulas a horarios</a>
              <a href="/gestion-aulas/consultar-disponibles" class="block hover:underline">Consultar aulas disponibles</a>
              <a href="/gestion-aulas/horarios" class="block hover:underline">Gestionar horario del docente</a>
            </div>
          </details>

          <details class="group">
            <summary class="flex items-center justify-between cursor-pointer px-3 py-2 rounded-lg hover:bg-slate-50">
              <span class="flex items-center gap-2"><span></span> Control de Asistencias</span>
              <span>></span>
            </summary>
            <div class="pl-4 py-2 space-y-1">
              <a href="/admin/control-asistencia" class="block hover:underline">Control de asistencia del docente</a>
            </div>
          </details>

          <details class="group">
            <summary class="flex items-center justify-between cursor-pointer px-3 py-2 rounded-lg hover:bg-slate-50">
              <span class="flex items-center gap-2"><span></span> Reportes y Estadisticas</span>
              <span>></span>
            </summary>
            <div class="pl-4 py-2 space-y-1">
              <a href="/reportes/globales" class="block hover:underline">Reportes estadisticos globales</a>
              <a href="/reportes/horarios-asistencia" class="block hover:underline">Reporte de horarios y asistencia</a>
            </div>
          </details>

          <details class="group">
            <summary class="flex items-center justify-between cursor-pointer px-3 py-2 rounded-lg hover:bg-slate-50">
              <span class="flex items-center gap-2"><span></span> Administracion</span>
              <span>></span>
            </summary>
            <div class="pl-4 py-2 space-y-1">
              <a href="/admin/bitacora" class="block hover:underline">Consultar bitacora</a>
            </div>
          </details>
        </nav>
      </div>
    </section>

    <!-- Columna derecha: tarjetas rapidas -->
    <section class="lg:col-span-2 space-y-6">
      <div class="bg-white rounded-2xl shadow p-6">
        <h3 class="font-semibold text-slate-900">Autenticacion y Seguridad</h3>
        <p class="text-slate-600 mb-4">Gestion de usuarios y permisos del sistema</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div class="rounded-xl border border-slate-200 bg-sky-50 p-4 text-center">
            <div class="text-slate-700">Usuarios</div>
            <div class="text-xl font-semibold text-slate-900">{{ $usuariosCount ?? '-' }}</div>
          </div>
          <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-center">
            <div class="text-slate-700">Docentes</div>
            <div class="text-xl font-semibold text-slate-900">{{ $docentesCount ?? '-' }}</div>
          </div>
          <div class="rounded-xl border border-purple-200 bg-purple-50 p-4 text-center">
            <div class="text-slate-700">Administrativos</div>
            <div class="text-xl font-semibold text-slate-900">{{ $administrativosCount ?? '-' }}</div>
          </div>
        </div>
        <div class="mt-4">
          <a href="/admin/usuarios" class="inline-flex items-center justify-center w-full md:w-auto bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Gestionar Usuarios</a>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow p-6">
        <h3 class="font-semibold text-slate-900">Gestion Academica</h3>
        <p class="text-slate-600 mb-4">Operaciones frecuentes</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <a href="/admin/materias" class="block rounded-xl border border-slate-200 hover:border-slate-300 p-4">
            <div class="font-medium text-slate-800">Registrar materias</div>
            <div class="text-slate-500 text-sm">Alta rapida de materias</div>
          </a>
          <a href="/admin/cargas" class="block rounded-xl border border-slate-200 hover:border-slate-300 p-4">
            <div class="font-medium text-slate-800">Asignar carga horaria</div>
            <div class="text-slate-500 text-sm">Vincular docente, materia y grupo</div>
          </a>
          <div class="rounded-xl border border-dashed border-slate-200 p-4 text-slate-400">
            Importar Excel/CSV (proximamente)
          </div>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
