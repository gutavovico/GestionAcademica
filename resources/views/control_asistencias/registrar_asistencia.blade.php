<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrar asistencia</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#eef5ff]">
  <header class="max-w-5xl mx-auto px-6 py-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Registrar asistencia</h1>
      <p class="text-slate-600">{{ $user?->nombre ?? 'Docente' }}</p>
    </div>
    <a href="/dashboard" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">Volver al perfil</a>
  </header>

  <main class="max-w-5xl mx-auto px-6 pb-16 space-y-6">
    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200 flex items-center gap-2">
        <h2 class="font-semibold text-slate-900">Mis clases de hoy</h2>
        <span id="dia" class="text-slate-600 text-sm"></span>
      </div>
      <div id="lista" class="p-6 grid grid-cols-1 gap-3"></div>
      <div id="msg" class="p-6 text-slate-500"></div>
    </section>

    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200 flex items-center gap-2">
        <h2 class="font-semibold text-slate-900">Asistencias registradas hoy</h2>
      </div>
      <div id="lista-ok" class="p-6 grid grid-cols-1 gap-3"></div>
      <div id="msg-ok" class="p-6 text-slate-500"></div>
    </section>
  </main>

  <script>
    const qs = s => document.querySelector(s)
    function fmt(t){ return (t||'').toString().slice(0,5) }
    function card(it){
      const aula = (it.nroaula? ('Aula '+it.nroaula) : '-') + (it.id_modulo? (' · Módulo '+it.id_modulo) : '')
      return `<div class="border border-slate-200 rounded-xl p-4 flex items-center justify-between">
        <div>
          <div class="font-medium text-slate-900">${it.sigla||''} ${it.materia||''} <span class="text-slate-500">(${it.grupo||''})</span></div>
          <div class="text-slate-700">${fmt(it.hora_ini)} - ${fmt(it.hora_fin)} · ${aula}</div>
        </div>
        <div class="flex gap-2">
          <button class="reg bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg px-3 py-1.5" data-id="${it.id_horario}" data-t="Presente">Registrar</button>
          <button class="justi-btn bg-amber-500 hover:bg-amber-600 text-white rounded-lg px-3 py-1.5" data-id="${it.id_horario}" data-tipo="Retraso">Retraso</button>
          <button class="justi-btn bg-rose-600 hover:bg-rose-700 text-white rounded-lg px-3 py-1.5" data-id="${it.id_horario}" data-tipo="Ausente">Ausente</button>
        </div>
      </div>
      <div class="mt-2 hidden" id="justi-${it.id_horario}">
        <label class="text-sm text-slate-700">Justificación (opcional) – <span id="tipo-${it.id_horario}" class="font-medium"></span></label>
        <textarea id="obs-${it.id_horario}" rows="2" class="border rounded-lg p-2 w-full bg-white text-slate-900" placeholder="Escribe el motivo..."></textarea>
        <div class="mt-2 flex gap-2">
          <button class="guardar-justi bg-neutral-900 hover:bg-black text-white rounded-lg px-3 py-1.5" data-id="${it.id_horario}">Guardar</button>
          <button class="cancelar-justi border border-slate-300 rounded-lg px-3 py-1.5" data-id="${it.id_horario}">Cancelar</button>
        </div>
      </div>`
    }
    function chip(tipo){
      const m = { Presente:'bg-emerald-100 text-emerald-700', Retraso:'bg-amber-100 text-amber-700', Ausente:'bg-rose-100 text-rose-700' }
      return `<span class="px-2 py-0.5 rounded ${m[tipo]||'bg-slate-100 text-slate-700'}">${tipo||'-'}</span>`
    }
    function cardOk(it){
      const aula = (it.nroaula? ('Aula '+it.nroaula) : '-') + (it.id_modulo? (' · Módulo '+it.id_modulo) : '')
      const obs = (it.observacion && String(it.observacion).trim()) ? `<div class="text-slate-500 text-sm">Obs.: ${String(it.observacion).trim()}</div>` : ''
      return `<div class="border border-slate-200 rounded-xl p-4 flex items-center justify-between">
        <div>
          <div class="font-medium text-slate-900">${it.sigla||''} ${it.materia||''} <span class="text-slate-500">(${it.grupo||''})</span></div>
          <div class="text-slate-700">${fmt(it.hora_ini)} - ${fmt(it.hora_fin)} · ${aula}</div>
          <div class="text-slate-600 text-sm">Registrado a las ${fmt(it.hora_registro||'')} · ${chip(it.tipo)}</div>
          ${obs}
        </div>
      </div>`
    }
    async function getJson(url, opt){ const r = await fetch(url, Object.assign({ headers:{'Accept':'application/json'} }, opt||{})); const ct=r.headers.get('content-type')||''; const d = ct.includes('application/json')? await r.json(): await r.text(); if(!r.ok) throw new Error(typeof d==='string'? d: (d.error||'Error')); return d }
    async function cargar(){
      qs('#msg').textContent = 'Cargando...'
      try{
        const d = await getJson('/docente/horarios-hoy')
        qs('#dia').textContent = d.dia? '· '+d.dia : ''
        const cont = qs('#lista'); cont.innerHTML = ''
        const okc = qs('#lista-ok'); okc.innerHTML = ''
        const items = Array.isArray(d.pendientes)? d.pendientes: []
        const oks = Array.isArray(d.registrados)? d.registrados: []
        if(items.length===0){ qs('#msg').textContent = 'No tienes clases pendientes para hoy.' } else { qs('#msg').textContent='' }
        items.forEach(it => cont.insertAdjacentHTML('beforeend', card(it)))
        if(oks.length===0){ qs('#msg-ok').textContent = 'Aún no registraste asistencias hoy.' } else { qs('#msg-ok').textContent='' }
        oks.forEach(it => okc.insertAdjacentHTML('beforeend', cardOk(it)))
      }catch(e){ qs('#msg').textContent = e.message||'Error al cargar' }
    }

    function flash(t, ok=true){ const el = qs('#msg'); el.textContent = t||''; el.className = 'p-6 ' + (ok? 'text-emerald-700' : 'text-rose-700'); setTimeout(()=>{ el.textContent=''; el.className='p-6 text-slate-500' }, 2500) }

    async function registrar(id, tipo, obs){
      try{
        qs('#msg').textContent = 'Registrando...'
        const payload = { id_horario: id, tipo, metodo:'Manual' }
        if(obs && obs.trim()) payload.observacion = obs.trim()
        const d = await getJson('/docente/registrar-asistencia', { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':'{{ csrf_token() }}'}, body: JSON.stringify(payload) })
        flash(d.ok || 'Registrado', true)
        await cargar()
      }catch(e){ flash(e.message||'Error al registrar', false) }
    }

    function showJusti(id, tipo){
      const p = document.getElementById('justi-'+id); if(!p) return; p.classList.remove('hidden'); p.dataset.type = tipo; const s = document.getElementById('tipo-'+id); if(s) s.textContent = tipo
    }

    document.addEventListener('click', (ev)=>{
      if(ev.target.closest('button.reg')){
        const b = ev.target.closest('button.reg'); return registrar(b.dataset.id, b.dataset.t)
      }
      if(ev.target.closest('button.justi-btn')){
        const b = ev.target.closest('button.justi-btn'); return showJusti(b.dataset.id, b.dataset.tipo)
      }
      if(ev.target.closest('button.guardar-justi')){
        const b = ev.target.closest('button.guardar-justi'); const id=b.dataset.id; const p=document.getElementById('justi-'+id); const tipo=p?.dataset?.type||'Retraso'; const obs = (document.getElementById('obs-'+id)?.value)||''; registrar(id, tipo, obs); if(p){ p.classList.add('hidden'); const t = document.getElementById('obs-'+id); if(t) t.value=''; }
      }
      if(ev.target.closest('button.cancelar-justi')){
        const b = ev.target.closest('button.cancelar-justi'); const id=b.dataset.id; const p=document.getElementById('justi-'+id); if(p){ p.classList.add('hidden'); const t=document.getElementById('obs-'+id); if(t) t.value='' }
      }
    })
    cargar()
  </script>
</body>
</html>

