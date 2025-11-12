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

      <!-- Gestionar horario de docente -->
      <section id="sec-gestionar" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Gestionar horario de docente</h2>
          <p class="text-slate-600 mb-3">Abre el gestor para consultar, crear y editar horarios de los docentes.</p>
          <a href="/gestion-aulas/horarios" class="inline-flex items-center bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Abrir gestor de horarios</a>
        </div>
      </section>

      <!-- Validar y aprobar horarios (placeholder) -->
      <section id="sec-validar" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Validar y aprobar horarios</h2>
          <p class="text-slate-600">Seccion en construccion.</p>
        </div>
      </section>

      <!-- Historial de asistencia -->
      <section id="sec-historial" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Historial de asistencia del docente</h2>
          <form id="f-hist-coord" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
            <label class="block md:col-span-2">
              <span class="text-slate-700 text-sm">Docente</span>
              <select id="c_docente" class="border rounded-lg p-2 w-full"></select>
            </label>
            <label class="block">
              <span class="text-slate-700 text-sm">Desde</span>
              <input type="date" id="c_desde" class="border rounded-lg p-2 w-full" />
            </label>
            <label class="block">
              <span class="text-slate-700 text-sm">Hasta</span>
              <input type="date" id="c_hasta" class="border rounded-lg p-2 w-full" />
            </label>
            <label class="block">
              <span class="text-slate-700 text-sm">Tipo</span>
              <select id="c_tipo" class="border rounded-lg p-2 w-full">
                <option value="">Todos</option>
                <option>Presente</option>
                <option>Ausente</option>
                <option>Retraso</option>
              </select>
            </label>
            <button id="c_buscar" type="button" class="bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Buscar</button>
          </form>
          <div class="overflow-auto mt-4">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="bg-slate-50 text-slate-700">
                  <th class="p-2 text-left">Docente</th>
                  <th class="p-2 text-left">Fecha</th>
                  <th class="p-2 text-left">Materia</th>
                  <th class="p-2 text-left">Grupo</th>
                  <th class="p-2 text-left">Tipo</th>
                  <th class="p-2 text-left">Método</th>

                  <th class="p-2 text-left">Obs.</th>
                </tr>
              </thead>
              <tbody id="tb-hist-coord" class="divide-y"></tbody>
            </table>
          </div>
          <div id="coord-msg" class="text-slate-500 text-sm mt-3"></div>
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
      async function cargarDocentes(){
        const sel = document.getElementById('c_docente')
        if(!sel) return
        sel.innerHTML = '<option value="">Seleccione docente</option>'
        try{
          const res = await fetch('/api/horarios/docentes', {headers:{'Accept':'application/json'}})
          if(!res.ok) return
          const data = await res.json()
          ;(data.docentes||[]).forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id_usuario; opt.textContent = `${d.nombre}${d.materias? ' · '+d.materias : ''}`;
            sel.appendChild(opt)
          })
        }catch(_){ /* noop */ }
      }

      async function cargarHist(){
        const sel = document.getElementById('c_docente')
        const tbody = document.getElementById('tb-hist-coord')
        const msg = document.getElementById('coord-msg')
        const idu = sel.value
        if(!idu){ msg.textContent = 'Seleccione un docente'; tbody.innerHTML=''; return }
        const desde = document.getElementById('c_desde').value
        const hasta = document.getElementById('c_hasta').value
        const tipo = document.getElementById('c_tipo').value
        msg.textContent = 'Cargando...'; tbody.innerHTML = ''
        const params = new URLSearchParams({ id_usuario: idu })
        if(desde) params.set('fecha_desde', desde)
        if(hasta) params.set('fecha_hasta', hasta)
        if(tipo) params.set('tipo', tipo)
        try{
          const res = await fetch('/coordinador/historial-asistencia?'+params.toString(), {headers:{'Accept':'application/json'}})
          if(!res.ok){ msg.textContent = 'No se pudo cargar el historial'; return }
          const data = await res.json();
          const fmt = t => (t||'').toString().slice(0,5)
          let rows = 0
          ;(data.items||[]).forEach(it => {
            const tr = document.createElement('tr')
            tr.innerHTML = `
              <td class="p-2">${it.docente||'-'}</td>
              <td class="p-2">${it.fecha_registro||''}</td>
              <td class="p-2">${(it.sigla?it.sigla+' ':'') + (it.materia||'')}</td>
              <td class="p-2">${it.grupo||'-'}</td>
              <td class="p-2">${it.tipo||'-'}</td>
              <td class="p-2">${it.metodo_registro||'-'}</td>

              <td class="p-2">${(it.observacion||'').slice(0,40)}</td>`
            tbody.appendChild(tr)
            rows++
          })
          if(rows===0){ const tr=document.createElement('tr'); tr.innerHTML='<td class="p-2" colspan="8">Sin resultados</td>'; tbody.appendChild(tr) }
          msg.textContent = ''
        }catch(_){ msg.textContent = 'Error al cargar datos' }
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
        if(name==='historial'){ cargarDocentes(); }
      }
      menu.addEventListener('click', (e)=>{
        if(e.target.matches('button[data-section]')){
          show(e.target.dataset.section)
        }
      })
      document.addEventListener('click', (e)=>{ if(e.target && e.target.id==='c_buscar'){ cargarHist() } })
      show('perfil')
    })()
  </script>
</body>
</html>
