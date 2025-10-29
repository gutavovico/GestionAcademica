<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal Docente</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#f3f6fb]">
  <div class="min-h-screen grid grid-cols-1 md:grid-cols-[260px_1fr]">
    <!-- Sidebar -->
    <aside class="bg-white border-r border-slate-200 p-4 flex flex-col">
      <div class="mb-6">
        <div class="text-slate-800 text-lg font-semibold">Portal Docente</div>
        <div class="text-slate-500 text-sm">Sistema Educativo</div>
      </div>

      <nav class="space-y-2" id="menu-docente">
        <button data-section="perfil" class="w-full text-left px-3 py-2 rounded-lg bg-cyan-500 text-white">Mi Perfil</button>
        <button data-section="horario" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Horario semanal</button>
        <button data-section="asistencia" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Control de asistencia</button>
        <button data-section="historial" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Historial de asistencia</button>
        <button data-section="carga" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Carga horaria</button>
      </nav>

      <form class="mt-auto pt-6" method="POST" action="/logout">
        @csrf
        <button class="w-full inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">Cerrar sesion</button>
      </form>
    </aside>

    <!-- Contenido -->
    <main class="p-6">
      <header class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Bienvenido, {{ $user->nombre ?? 'Docente' }}</h1>
        <p class="text-slate-600">Aqui puedes gestionar tu informacion academica</p>
      </header>

      <!-- Secci�n: Perfil -->
      <section id="sec-perfil" class="space-y-6">
        <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-6">
          <div class="h-20 w-20 rounded-full bg-cyan-500 text-white flex items-center justify-center text-2xl font-bold">
            {{ strtoupper(mb_substr($user->nombre ?? 'D',0,1)) }}
          </div>
          <div>
            <div class="text-slate-900 text-lg font-semibold">{{ $user->nombre ?? 'Docente' }}</div>
            <div class="text-slate-600">Correo: {{ $user->correo ?? '-' }}</div>
            <div class="text-slate-600">Telefono: {{ $user->telefono ?? '-' }}</div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Informacion de Contacto</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-slate-50 rounded-lg p-4">
              <div class="text-xs uppercase text-slate-500">Correo electronico</div>
              <div class="text-slate-800">{{ $user->correo ?? '-' }}</div>
            </div>
            <div class="bg-slate-50 rounded-lg p-4">
              <div class="text-xs uppercase text-slate-500">Telefono</div>
              <div class="text-slate-800">{{ $user->telefono ?? '-' }}</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Secci�n: Horario semanal -->
      <section id="sec-horario" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Horario semanal</h2>
          <div class="overflow-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="bg-slate-50 text-slate-700">
                  <th class="p-2 text-left">Dia</th>
                  <th class="p-2 text-left">Materia</th>
                  <th class="p-2 text-left">Grupo</th>
                  <th class="p-2 text-left">Aula</th>
                  <th class="p-2 text-left">Hora</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                <tr>
                  <td class="p-2">-</td><td class="p-2">-</td><td class="p-2">-</td><td class="p-2">-</td><td class="p-2">-</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p class="text-slate-500 text-sm mt-3">Este es un ejemplo. Podemos poblarlo desde la tabla horario.</p>
        </div>
      </section>

      <!-- Secci�n: Control de asistencia -->
      <section id="sec-asistencia" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Control de asistencia</h2>
          <form class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input class="border rounded-lg p-2" placeholder="Materia">
            <input class="border rounded-lg p-2" placeholder="Grupo">
            <input class="border rounded-lg p-2" type="date">
            <button class="bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Registrar</button>
          </form>
          <p class="text-slate-500 text-sm mt-3">Aqui podros registrar tu asistencia (QR/Manual) segun el diseno que definamos.</p>
        </div>
      </section>

      <!-- Secci�n: Historial de asistencia -->
      <section id="sec-historial" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Historial de asistencia</h2>
          <div class="overflow-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="bg-slate-50 text-slate-700">
                  <th class="p-2 text-left">Fecha</th>
                  <th class="p-2 text-left">Materia</th>
                  <th class="p-2 text-left">Grupo</th>
                  <th class="p-2 text-left">Tipo</th>
                  <th class="p-2 text-left">Metodo</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                <tr>
                  <td class="p-2">-</td><td class="p-2">-</td><td class="p-2">-</td><td class="p-2">-</td><td class="p-2">-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Secci�n: Carga horaria -->
      <section id="sec-carga" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Mi carga horaria</h2>
          <div class="overflow-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="bg-slate-50 text-slate-700">
                  <th class="p-2 text-left">Materia</th>
                  <th class="p-2 text-left">Grupo</th>
                  <th class="p-2 text-left">Horas/semana</th>
                  <th class="p-2 text-left">Gestion</th>
                  <th class="p-2 text-left">Estado</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                <tr>
                  <td class="p-2">-</td><td class="p-2">-</td><td class="p-2">-</td><td class="p-2">-</td><td class="p-2">-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    (function () {
      const menu = document.getElementById('menu-docente')
      const sections = {
        perfil: document.getElementById('sec-perfil'),
        horario: document.getElementById('sec-horario'),
        asistencia: document.getElementById('sec-asistencia'),
        historial: document.getElementById('sec-historial'),
        carga: document.getElementById('sec-carga'),
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
      // por defecto
      show('perfil')
    })()
  </script>
</body>
</html>

