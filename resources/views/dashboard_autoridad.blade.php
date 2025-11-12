<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal Decano</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#f3f6fb]">
  <div class="min-h-screen grid grid-cols-1 md:grid-cols-[260px_1fr]">
    <aside class="bg-white border-r border-slate-200 p-4 flex flex-col">
      <div class="mb-6">
        <div class="text-slate-800 text-lg font-semibold">Portal Decano</div>
        <div class="text-slate-500 text-sm">Sistema Educativo</div>
      </div>
      <nav class="space-y-2" id="menu-decano">
        <button data-section="perfil" class="w-full text-left px-3 py-2 rounded-lg bg-cyan-500 text-white">Mi Perfil</button>
        <button data-section="admin" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Administracion</button>
        <button data-section="horarios" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Gestion de Horarios</button>
      </nav>
      <form class="mt-auto pt-6" method="POST" action="/logout">
        @csrf
        <button class="w-full inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">Cerrar sesion</button>
      </form>
    </aside>

    <main class="p-6">
      <header class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Bienvenido, {{ $user->nombre ?? 'Decano' }}</h1>
        <p class="text-slate-600">Aqui puedes consultar informacion y reportes</p>
      </header>

      <section id="sec-perfil" class="space-y-6">
        <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-6">
          <div class="h-20 w-20 rounded-full bg-cyan-500 text-white flex items-center justify-center text-2xl font-bold">
            {{ strtoupper(mb_substr($user->nombre ?? 'D',0,1)) }}
          </div>
          <div>
            <div class="text-slate-900 text-lg font-semibold">{{ $user->nombre ?? 'Decano' }}</div>
            <div class="text-slate-600">Correo: {{ $user->correo ?? '-' }}</div>
            <div class="text-slate-600">Telefono: {{ $user->telefono ?? '-' }}</div>
          </div>
        </div>
      </section>

      <section id="sec-admin" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Administracion</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <a href="/reportes/globales" class="block rounded-xl border border-slate-200 hover:border-slate-300 p-4">
              <div class="font-medium text-slate-800">Reportes estadisticos globales</div>
              <div class="text-slate-500 text-sm">Uso de aulas, carga y asistencia</div>
            </a>
            <a href="/autoridad/bitacora" class="block rounded-xl border border-slate-200 hover:border-slate-300 p-4">
              <div class="font-medium text-slate-800">Consultar bitacora</div>
              <div class="text-slate-500 text-sm">Registro de acciones del sistema</div>
            </a>
          </div>
        </div>
      </section>

      <section id="sec-horarios" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Gestión de Horarios</h2>
          <p class="text-slate-600 mb-3">Consulta y revisa los horarios de los docentes.</p>
          <a href="/gestion-aulas/horarios" class="inline-flex items-center bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Abrir gestor de horarios</a>
        </div>
      </section>
    </main>
  </div>

  <script>
    (function () {
      const menu = document.getElementById('menu-decano')
      const sections = {
        perfil: document.getElementById('sec-perfil'),
        admin: document.getElementById('sec-admin'),
        horarios: document.getElementById('sec-horarios'),
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
