<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Control de asistencia - Administrador</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#eef5ff]">
  <header class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Control de asistencia</h1>
      <p class="text-slate-600">Gestionar registros de asistencia por docente</p>
    </div>
    <a href="/dashboard" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">Volver al panel</a>
  </header>

  <main class="max-w-6xl mx-auto px-6 pb-16 space-y-6">
    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200"><h2 class="font-semibold text-slate-900">Filtros</h2></div>
      <div class="p-6">
        <form id="filtros" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
          <label class="block md:col-span-2">
            <span class="text-slate-700 text-sm">Día</span>
            <select id="dia" class="border rounded-lg p-2 w-full bg-white text-slate-900">
              <option>Lunes</option><option>Martes</option><option>Miércoles</option><option>Jueves</option><option>Viernes</option><option>Sábado</option><option>Domingo</option>
            </select>
          </label>
          <label class="block md:col-span-3">
            <span class="text-slate-700 text-sm">Docente</span>
            <input id="nombre" class="border rounded-lg p-2 w-full bg-white text-slate-900" placeholder="Buscar por nombre" />
          </label>
          <div class="flex gap-2">
            <button id="buscar" type="button" class="bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Buscar</button>
            <button id="limpiar" type="button" class="border border-slate-300 rounded-lg px-4 py-2">Limpiar</button>
          </div>
        </form>
      </div>
    </section>

    <!-- Registradas primero -->
    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200 flex items-center gap-2 justify-between">
        <h2 class="font-semibold text-slate-900">Asistencias registradas hoy</h2>
      </div>
      <div class="p-6 overflow-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-slate-50 text-slate-700">
              <th class="p-2 text-left">Docente</th>
              <th class="p-2 text-left">Materia</th>
              <th class="p-2 text-left">Grupo</th>
              <th class="p-2 text-left">Aula</th>
              <th class="p-2 text-left">Hora</th>
              <th class="p-2 text-left">Estado</th>
              <th class="p-2 text-left">Registrado</th>
              <th class="p-2 text-left">Observación</th>
            </tr>
          </thead>
          <tbody id="tbody-ok" class="divide-y"></tbody>
        </table>
        <div id="msg-ok" class="text-slate-500 text-sm mt-3"></div>
      </div>
    </section>

    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200 flex items-center gap-2 justify-between">
        <h2 class="font-semibold text-slate-900">Pendientes de registrar (solo lectura)</h2>
        <span id="count" class="text-slate-500 text-sm"></span>
      </div>
      <div class="p-6 overflow-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-slate-50 text-slate-700">
              <th class="p-2 text-left">Docente</th>
              <th class="p-2 text-left">Materia</th>
              <th class="p-2 text-left">Grupo</th>
              <th class="p-2 text-left">Aula</th>
              <th class="p-2 text-left">Hora</th>
            </tr>
          </thead>
          <tbody id="tbody-pend" class="divide-y"></tbody>
        </table>
        <div id="msg" class="text-slate-500 text-sm mt-3"></div>
      </div>
    </section>
  </main>

  <script>
    const qs = s => document.querySelector(s)
    const qv = s => (qs(s).value||'').trim()
    function setMsg(t){ qs('#msg').textContent = t||'' }
    function setCount(n){ qs('#count').textContent = n? (n+ ' registros') : '' }
    function flash(t, ok=true){ const el = qs('#msg'); el.textContent = t||''; el.className = 'text-sm mt-3 ' + (ok? 'text-emerald-700' : 'text-rose-700'); setTimeout(()=>{ el.textContent=''; el.className='text-slate-500 text-sm mt-3' }, 2500) }
    const chip = (tipo) => { const m = { Presente:'bg-emerald-100 text-emerald-700', Retraso:'bg-amber-100 text-amber-700', Ausente:'bg-rose-100 text-rose-700' }; return `<span class="px-2 py-0.5 rounded ${m[tipo]||'bg-slate-100 text-slate-700'}">${tipo||'-'}</span>` }

    async function getJson(url, opt){ const r = await fetch(url, Object.assign({headers:{'Accept':'application/json'}}, opt||{})); const ct=r.headers.get('content-type')||''; const d=ct.includes('application/json')? await r.json(): await r.text(); if(!r.ok) throw new Error(typeof d==='string'? d : (d.error||'Error')); return d }

    async function listar(){
      setMsg('Cargando...'); setCount(''); const tbp = qs('#tbody-pend'); const tbo = qs('#tbody-ok'); tbp.innerHTML=''; tbo.innerHTML=''
      const dia = qv('#dia')||'Lunes'; const nombre = qv('#nombre');
      const p = new URLSearchParams({ dia }); if(nombre) p.set('nombre', nombre)
      try{
        const data = await getJson('/admin/horarios-hoy?'+p.toString())
        const pend = Array.isArray(data.pendientes)? data.pendientes: []
        const oks  = Array.isArray(data.registrados)? data.registrados: []
        setCount(pend.length)
        if(oks.length===0){ qs('#msg-ok').textContent = 'Aún no hay registros hoy.' } else { qs('#msg-ok').textContent = '' }
        oks.forEach(it => {
          const aula = (it.nroaula? ('Aula '+it.nroaula) : '-') + (it.id_modulo? (' - Módulo '+it.id_modulo) : '')
          const tr = document.createElement('tr'); tr.innerHTML = `
            <td class="p-2 text-slate-800">${it.docente||'-'}</td>
            <td class="p-2 text-slate-800">${(it.sigla||'')+' '+(it.materia||'')}</td>
            <td class="p-2 text-slate-800">${it.grupo||'-'}</td>
            <td class="p-2 text-slate-800">${aula}</td>
            <td class="p-2 text-slate-800">${String(it.hora_ini||'').slice(0,5)} - ${String(it.hora_fin||'').slice(0,5)}</td>
            <td class="p-2">${chip(it.tipo)}</td>
            <td class="p-2">${String(it.hora_registro||'').slice(0,5)}</td>
            <td class="p-2">${(it.observacion||'')}</td>`; tbo.appendChild(tr)
        })

        if(pend.length===0){ setMsg('Sin pendientes') } else { setMsg('') }
        pend.forEach(it => {
          const aula = (it.nroaula? ('Aula '+it.nroaula) : '-') + (it.id_modulo? (' - Módulo '+it.id_modulo) : '')
          const tr = document.createElement('tr'); tr.innerHTML = `
            <td class="p-2 text-slate-800">${it.docente||'-'}</td>
            <td class="p-2 text-slate-800">${(it.sigla||'')+' '+(it.materia||'')}</td>
            <td class="p-2 text-slate-800">${it.grupo||'-'}</td>
            <td class="p-2 text-slate-800">${aula}</td>
            <td class="p-2 text-slate-800">${String(it.hora_ini||'').slice(0,5)} - ${String(it.hora_fin||'').slice(0,5)}</td>`; tbp.appendChild(tr)
        })
      }catch(e){ setMsg(e.message||'Error al cargar') }
    }
    // Sin acciones para admin
    qs('#buscar').addEventListener('click', listar)
    qs('#limpiar').addEventListener('click', ()=>{ qs('#nombre').value=''; qs('#dia').selectedIndex=0; listar() })
    ;(async function init(){ await listar() })()
  </script>
</body>
</html>
