<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal Coordinador</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#f3f6fb]">
  <div class="min-h-screen grid grid-cols-1 md:grid-cols-[260px_1fr]">
    <!-- Sidebar -->
    <aside class="bg-white border-r border-slate-200 p-4 flex flex-col">
      <div class="mb-6">
        <div class="text-slate-800 text-lg font-semibold">Portal Coordinador</div>
        <div class="text-slate-500 text-sm">Sistema Educativo</div>
      </div>

      <nav class="space-y-2" id="menu-coord">
        <button data-section="perfil" class="w-full text-left px-3 py-2 rounded-lg bg-cyan-500 text-white">Mi Perfil</button>
        <button data-section="importar" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Importar Excel/CSV</button>
        <button data-section="gestionar" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Gestionar horario de docente</button>
        <button data-section="validar" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Validar y aprobar horarios</button>
        <button data-section="historial" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Historial de asistencia</button>
      </nav>

      <form class="mt-auto pt-6" method="POST" action="/logout">
        @csrf
        <button class="w-full inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">Cerrar sesion</button>
      </form>
    </aside>

    <!-- Contenido -->
    <main class="p-6">
      <header class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Bienvenido, {{ $user->nombre ?? 'Coordinador' }}</h1>
        <p class="text-slate-600">Herramientas para coordinacion de horarios y asistencias</p>
      </header>

      <!-- Perfil -->
      <section id="sec-perfil" class="space-y-6">
        <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-6">
          <div class="h-20 w-20 rounded-full bg-cyan-500 text-white flex items-center justify-center text-2xl font-bold">
            {{ strtoupper(mb_substr($user->nombre ?? 'C',0,1)) }}
          </div>
          <div>
            <div class="text-slate-900 text-lg font-semibold">{{ $user->nombre ?? 'Coordinador' }}</div>
            <div class="text-slate-600">Correo: {{ $user->correo ?? '-' }}</div>
            <div class="text-slate-600">Telefono: {{ $user->telefono ?? '-' }}</div>
          </div>
        </div>
      </section>

      <!-- Importar Excel/CSV (placeholder) -->
      <section id="sec-importar" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Importar Excel/CSV</h2>
          <p class="text-slate-600">Seccion en construccion.</p>
        </div>
      </section>

      <!-- Gestionar horario de docente (placeholder) -->
      <section id="sec-gestionar" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Gestionar horario de docente</h2>
          <p class="text-slate-600">Seccion en construccion.</p>
        </div>
      </section>

      <!-- Validar y aprobar horarios (placeholder) -->
      <section id="sec-validar" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Validar y aprobar horarios</h2>
          <p class="text-slate-600">Seccion en construccion.</p>
        </div>
      </section>

      <!-- Historial de asistencia (placeholder) -->
      <section id="sec-historial" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Historial de asistencia del docente</h2>
          <p class="text-slate-600">Seccion en construccion.</p>
        </div>
      </section>
    </main>
  </div>

  <script>
    (function () {
      const menu = document.getElementById('menu-coord')
      const sections = {
        perfil: document.getElementById('sec-perfil'),
        importar: document.getElementById('sec-importar'),
        gestionar: document.getElementById('sec-gestionar'),
        validar: document.getElementById('sec-validar'),
        historial: document.getElementById('sec-historial'),
      }
      function show(name){
        Object.values(sections).forEach(s => s.classList.add('hidden'))
        sections[name]?.classList.remove('hidden')
        ;[...menu.querySelectorAll('button')].forEach(b=>{
          b.classList.remove('bg-cyan-500','text-white');
          b.classList.add('hover:bg-slate-50')
        })
        const active = menu.querySelector(`[data-section="${name}"]`)
        active?.classList.add('bg-cyan-500','text-white')
        active?.classList.remove('hover:bg-slate-50')
      }
      menu.addEventListener('click', (e)=>{
        if(e.target.matches('button[data-section]')){
          show(e.target.dataset.section)
        }
      })
      show('perfil')
    })()
  </script>
</body>
</html>

