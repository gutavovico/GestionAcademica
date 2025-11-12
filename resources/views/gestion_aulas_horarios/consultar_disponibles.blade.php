<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Consultar aulas disponibles</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root { color-scheme: light dark }
    .hint { color:#64748b }
  </style>
  <!-- Nota: Tailwind por CDN solo para desarrollo -->
  <!-- Para producción, migrar a Vite/PostCSS -->
  <!-- https://tailwindcss.com/docs/installation -->
  
</head>
<body class="min-h-screen bg-[#eef5ff]">
  <header class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Consultar aulas disponibles</h1>
      <p class="hint">Filtra por día(s) y rango de horas. Opcional: capacidad, módulo.</p>
    </div>
    <a href="/dashboard" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">Volver al perfil</a>
  </header>

  <main class="max-w-6xl mx-auto px-6 pb-16 space-y-6">
    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200">
        <h2 class="font-semibold text-slate-900">Filtros</h2>
      </div>
      <div class="p-6">
        <form id="filtros" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
          <label class="block md:col-span-2">
            <span class="text-slate-700 text-sm">Día</span>
            <select id="dia" class="border rounded-lg p-2 w-full bg-white text-slate-900">
              <option>Lunes</option>
              <option>Martes</option>
              <option>Miércoles</option>
              <option>Jueves</option>
              <option>Viernes</option>
              <option>Sábado</option>
              <option>Domingo</option>
            </select>
          </label>
          <label class="block">
            <span class="text-slate-700 text-sm">Hora inicio</span>
            <input id="ini" type="time" class="border rounded-lg p-2 w-full bg-white text-slate-900" />
          </label>
          <label class="block">
            <span class="text-slate-700 text-sm">Hora fin</span>
            <input id="fin" type="time" class="border rounded-lg p-2 w-full bg-white text-slate-900" />
          </label>
          <label class="block">
            <span class="text-slate-700 text-sm">Capacidad mínima</span>
            <input id="cap" type="number" min="1" class="border rounded-lg p-2 w-full bg-white text-slate-900" value="1" />
          </label>
          <label class="block">
            <span class="text-slate-700 text-sm">Módulo (opcional)</span>
            <select id="mod" class="border rounded-lg p-2 w-full bg-white text-slate-900"></select>
          </label>
          <div class="md:col-span-2 flex gap-2">
            <button id="buscar" type="button" class="bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Buscar</button>
            <button id="limpiar" type="button" class="border border-slate-300 rounded-lg px-4 py-2">Limpiar</button>
          </div>
        </form>
      </div>
    </section>

    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200 flex items-center gap-2 justify-between">
        <h2 class="font-semibold text-slate-900">Resultados</h2>
        <span id="count" class="text-slate-500 text-sm"></span>
      </div>
      <div class="p-6 overflow-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-slate-50 text-slate-700">
              <th class="p-2 text-left">ID</th>
              <th class="p-2 text-left">Aula</th>
              <th class="p-2 text-left">Tipo</th>
              <th class="p-2 text-left">Capacidad</th>
              <th class="p-2 text-left">Módulo</th>
              <th class="p-2 text-left">Disponibilidad</th>
            </tr>
          </thead>
          <tbody id="tbody" class="divide-y"></tbody>
        </table>
        <div id="msg" class="text-slate-500 text-sm mt-3"></div>
      </div>
    </section>
  </main>

  <script>
    const qs = s => document.querySelector(s)
    const qv = s => (qs(s).value||'').trim()
    function setMsg(t){ qs('#msg').textContent = t||'' }
    function setCount(n){ qs('#count').textContent = n? (n+ ' aulas') : '' }

    async function getJson(url){
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } })
      const ct = res.headers.get('content-type') || ''
      let payload
      try {
        payload = ct.includes('application/json') ? await res.json() : await res.text()
      } catch(_) {
        payload = await res.text().catch(()=> '')
      }
      if(!res.ok){
        const msg = typeof payload === 'string' ? payload : (payload && (payload.error||payload.message)) || 'Error'
        throw new Error(msg)
      }
      return payload
    }

    async function cargarModulos(){
      try {
        const data = await getJson('/api/asignacion/modulos')
        const sel = qs('#mod'); sel.innerHTML = '<option value="">Todos</option>'
        ;(data.modulos||[]).forEach(m=>{ const op=document.createElement('option'); op.value=m.id_modulo; op.textContent=`${m.id_modulo} - ${m.facultad||''}`; sel.appendChild(op) })
      } catch(_){ /* silencio: el selector quedará vacío */ }
    }

    async function buscar(){
      setMsg('Cargando...'); setCount(''); const tb = qs('#tbody'); tb.innerHTML=''
      const capa = qv('#cap')||1; const ini = qv('#ini'); const fin = qv('#fin'); const dia = qv('#dia')||'Lunes'; const mod = qv('#mod')
      const hasTime = !!ini && !!fin;
      let items = []
      try{
        if(hasTime){
          if(ini >= fin){ setMsg('La hora inicio debe ser menor que la hora fin'); return }
          const p = new URLSearchParams({ dia, hora_ini:ini, hora_fin:fin, capacidad: String(capa) }); if(mod) p.set('id_modulo', mod)
          const data = await getJson('/api/asignacion/aulas-disponibles?'+p.toString())
          items = data.aulas || []
        } else {
          const p = new URLSearchParams({ capacidad: String(capa) }); if(mod) p.set('id_modulo', mod)
          const data = await getJson('/api/asignacion/aulas?'+p.toString())
          items = data.aulas || []
        }
        setCount(items.length)
        if(items.length===0){ setMsg('No existen aulas disponibles.'); return }
        for(const a of items){ const tr = document.createElement('tr'); tr.innerHTML = `
            <td class="p-2 text-slate-800">${a.id_aula}</td>
            <td class="p-2 text-slate-800">${a.nroaula}</td>
            <td class="p-2 text-slate-800">${a.tipo_aula||'-'}</td>
            <td class="p-2 text-slate-800">${a.capacidad}</td>
            <td class="p-2 text-slate-800">${a.id_modulo||'-'}</td>
            <td class="p-2 text-slate-700"><span class="text-slate-400">cargando...</span></td>`; tb.appendChild(tr)
          try{
            const diaSel = qv('#dia') || 'Lunes';
            const data2 = await getJson(`/api/asignacion/slots?dia=${encodeURIComponent(diaSel)}&id_aula=${a.id_aula}`)
            const libres = Array.isArray(data2.libres) ? data2.libres : [];
            const toMin = t => { const [h,m] = String(t||'0:0').split(':').map(Number); return h*60 + m };
            const toTxt = m => `${String(Math.floor(m/60)).padStart(2,'0')}:${String(m%60).padStart(2,'0')}`;
            const merge = (arr) => { if(!arr.length) return []; arr = arr.filter(s=>s && s.hora_ini && s.hora_fin).map(s=>({hora_ini:s.hora_ini, hora_fin:s.hora_fin}));
              arr.sort((x,y)=>toMin(x.hora_ini)-toMin(y.hora_ini)); const out=[{...arr[0]}];
              for(let i=1;i<arr.length;i++){ const prev=out[out.length-1]; const ci=toMin(arr[i].hora_ini), cf=toMin(arr[i].hora_fin), pf=toMin(prev.hora_fin); if(ci<=pf){ if(cf>pf) prev.hora_fin=toTxt(cf) } else { out.push({...arr[i]}) } }
              return out; };
            const rangos = merge(libres);
            tr.children[5].textContent = rangos.length ? rangos.map(r=>`${r.hora_ini}-${r.hora_fin}`).join(', ') : '-';
          }catch(_){ tr.children[5].innerHTML = '<span class="text-slate-400">-</span>'; }
        }
        setMsg('')
      }catch(e){ setMsg(typeof e?.message === 'string' && e.message ? e.message : 'Error de conexión') }
    }

    qs('#buscar').addEventListener('click', buscar)
    qs('#limpiar').addEventListener('click', ()=>{ ['#ini','#fin','#cap'].forEach(s=>qs(s).value=''); const sDia=qs('#dia'); if(sDia) sDia.selectedIndex=0; const sMod=qs('#mod'); if(sMod) sMod.selectedIndex=0; buscar() })
    ;(async function init(){ await cargarModulos(); buscar() })()
  </script>
</body>
</html>
