<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal Docente</title>
  <!-- Nota: CDN de Tailwind es para desarrollo -->
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
        <button data-section="perfil" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Mi Perfil</button>
        <button data-section="horario" class="w-full text-left px-3 py-2 rounded-lg bg-cyan-500 text-white">Horario semanal</button>
        <button data-section="asistencia" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50">Registro de asistencia</button>
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

      <!-- Seccion: Perfil -->
      <section id="sec-perfil" class="hidden space-y-6">
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
      </section>

      <!-- Seccion: Horario semanal -->
      <section id="sec-horario" class="space-y-4">
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
              <tbody id="tb-horario" class="divide-y"></tbody>
            </table>
          </div>
          <div id="horario-msg" class="text-slate-500 text-sm mt-3"></div>
          <div class="mt-3">
            <a href="/docente/horario-semanal" class="text-cyan-700 hover:underline">Ver en pantalla completa</a>
          </div>
        </div>
      </section>

      <!-- Seccion: Control de asistencia -->
      <section id="sec-asistencia" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Registro de asistencia</h2>
          <p class="text-slate-600 mb-3">Marca tu asistencia de forma rápida y segura.</p>
          <a href="/docente/registrar-asistencia" class="inline-flex items-center bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Ir a registrar asistencia</a>
        </div>
      </section>

      <!-- Seccion: Historial de asistencia -->
      <section id="sec-historial" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Historial de asistencia</h2>
          <form id="f-hist" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <label class="block">
              <span class="text-slate-700 text-sm">Desde</span>
              <input type="date" id="h_desde" class="border rounded-lg p-2 w-full" />
            </label>
            <label class="block">
              <span class="text-slate-700 text-sm">Hasta</span>
              <input type="date" id="h_hasta" class="border rounded-lg p-2 w-full" />
            </label>
            <label class="block">
              <span class="text-slate-700 text-sm">Materia</span>
              <select id="h_materia" class="border rounded-lg p-2 w-full"></select>
            </label>
            <label class="block">
              <span class="text-slate-700 text-sm">Tipo</span>
              <select id="h_tipo" class="border rounded-lg p-2 w-full">
                <option value="">Todos</option>
                <option>Presente</option>
                <option>Ausente</option>
                <option>Retraso</option>
              </select>
            </label>
            <button id="h_buscar" type="button" class="bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Buscar</button>
          </form>
          <div class="overflow-auto mt-4">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="bg-slate-50 text-slate-700">
                  <th class="p-2 text-left">Fecha</th>
                  <th class="p-2 text-left">Materia</th>
                  <th class="p-2 text-left">Grupo</th>
                  <th class="p-2 text-left">Tipo</th>
                  <th class="p-2 text-left">Hora</th>

                  <th class="p-2 text-left">Obs.</th>
                </tr>
              </thead>
              <tbody id="tb-hist" class="divide-y"></tbody>
            </table>
          </div>
          <div id="hist-msg" class="text-slate-500 text-sm mt-3"></div>
        </div>
      </section>

      <!-- Seccion: Carga horaria -->
      <section id="sec-carga" class="hidden space-y-4">
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="font-semibold text-slate-900 mb-3">Mi carga horaria</h2>
          <p class="text-slate-500">Proximamente</p>
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

      async function loadHorario(){
        const tbody = document.getElementById('tb-horario')
        const msg = document.getElementById('horario-msg')
        if(!tbody || !msg) return
        tbody.innerHTML = ''
        msg.textContent = 'Cargando...'
        try{
          const res = await fetch('/docente/mi-horario-semanal', {headers:{'Accept':'application/json'}})
          if(!res.ok){ msg.textContent = 'No se pudo cargar el horario'; return }
          const data = await res.json()
          const diasData = (data && data.dias) ? data.dias : {}
          const orden = ['Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo']
          const norm = s => (s||'').normalize ? (s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase() : (s||'').toLowerCase()
          const keyFor = d => Object.keys(diasData).find(k => norm(k) === norm(d)) || d
          let rows = 0
          const fmt = t => (t||'').toString().slice(0,5)
          orden.forEach(d => {
            const k = keyFor(d)
            const items = diasData[k] || []
            items.forEach(it => {
              const tr = document.createElement('tr')
              tr.innerHTML = `
                <td class="p-2">${d}</td>
                <td class="p-2">${(it.materia||'').trim() || '-'}</td>
                <td class="p-2">${it.grupo || '-'}</td>
                <td class="p-2">${it.aula || '-'}${it.modulo ? ' - Mod. '+it.modulo : ''}</td>
                <td class="p-2">${fmt(it.hora_ini)} - ${fmt(it.hora_fin)}</td>
                `
              tbody.appendChild(tr)
              rows++
            })
          })
          if(rows===0){
            const tr = document.createElement('tr')
            tr.innerHTML = '<td class="p-2" colspan="5">No tienes horarios asignados.</td>'
            tbody.appendChild(tr)
          }
          msg.textContent = ''
        }catch(e){
          msg.textContent = 'Error al cargar los datos'
        }
      }

      async function loadMateriasDocente(){
        const sel = document.getElementById('h_materia')
        if(!sel) return
        sel.innerHTML = '<option value="">Todas</option>'
        try{
          const res = await fetch('/api/horarios/cargas?id_usuario=0', {headers:{'Accept':'application/json'}})
          // fallback: si devuelve 422 porque falta id, ignoramos y no rompemos
        }catch(_){ /* noop */ }
        // Mejor: pedir al backend del docente
        try{
          const res = await fetch('/docente/mi-horario-semanal', {headers:{'Accept':'application/json'}})
          if(!res.ok) return
          const data = await res.json()
          const set = new Map()
          Object.values(data.dias||{}).forEach(list => {
            (list||[]).forEach(it => {
              const key = (it.materia||'').trim()
              if(key) set.set(key, it)
            })
          })
          Array.from(set.keys()).sort().forEach(k => {
            const opt = document.createElement('option'); opt.textContent = k; opt.value = k; sel.appendChild(opt)
          })
        }catch(_){ /* noop */ }
      }

      async function loadHistorial(){
        const tbody = document.getElementById('tb-hist')
        const msg = document.getElementById('hist-msg')
        const desde = document.getElementById('h_desde').value
        const hasta = document.getElementById('h_hasta').value
        const materiaTxt = document.getElementById('h_materia').value
        const tipo = document.getElementById('h_tipo').value
        tbody.innerHTML = ''
        msg.textContent = 'Cargando...'
        const params = new URLSearchParams()
        if(desde) params.set('fecha_desde', desde)
        if(hasta) params.set('fecha_hasta', hasta)
        if(tipo) params.set('tipo', tipo)
        // Nota: como no tenemos id_materia directo, filtramos por texto en backend opcionalmente
        if(materiaTxt) params.set('texto', materiaTxt)
        try{
          const res = await fetch('/docente/mi-historial-asistencia?'+params.toString(), {headers:{'Accept':'application/json'}})
          if(!res.ok){ msg.textContent = 'No se pudo cargar el historial'; return }
          const data = await res.json()
          const fmt = t => (t||'').toString().slice(0,5)
          let rows = 0
          ;(data.items||[]).forEach(it => {
            const tr = document.createElement('tr')
            tr.innerHTML = `
              <td class="p-2">${it.fecha_registro}</td>
              <td class="p-2">${(it.sigla?it.sigla+' ':'') + (it.materia||'')}</td>
              <td class="p-2">${it.grupo||'-'}</td>
              <td class="p-2">${it.tipo||'-'}</td>
              <td class="p-2">${fmt(it.hora_registro)}</td>
              
              <td class="p-2">${(it.observacion||'').slice(0,40)}</td>`
            tbody.appendChild(tr)
            rows++
          })
          if(rows===0){
            const tr = document.createElement('tr'); tr.innerHTML = '<td class="p-2" colspan="7">Sin resultados</td>'; tbody.appendChild(tr)
          }
          msg.textContent = ''
        }catch(e){ msg.textContent = 'Error al cargar datos' }
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
        if(name==='horario'){ loadHorario() }
      }
      menu.addEventListener('click', (e)=>{
        if(e.target.matches('button[data-section]')){
          show(e.target.dataset.section)
          if(e.target.dataset.section==='historial'){ loadMateriasDocente(); loadHistorial() }
        }
      })
      // Abrir Horario por defecto
      show('horario')
    })()
  </script>
</body>
</html>
