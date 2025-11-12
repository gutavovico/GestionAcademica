<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reporte de horarios y asistencia</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <header class="max-w-7xl mx-auto mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Reporte de horarios y asistencia</h1>
      <p class="text-slate-600">Filtra por docente, materia, grupo y rango de fechas</p>
    </div>
    <a href="/dashboard" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">Volver al dashboard</a>
  </header>

  <main class="max-w-7xl mx-auto space-y-6">
    <section class="bg-white rounded-2xl shadow p-6 border border-slate-200">
      <h2 class="font-semibold text-slate-900 mb-4">Parámetros</h2>
      <form id="filtros" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
        <label class="block">
          <span class="text-sm text-slate-700">Desde</span>
          <input type="date" id="desde" class="border rounded-lg p-2 w-full" />
        </label>
        <label class="block">
          <span class="text-sm text-slate-700">Hasta</span>
          <input type="date" id="hasta" class="border rounded-lg p-2 w-full" />
        </label>
        <label class="block">
          <span class="text-sm text-slate-700">Docente</span>
          <select id="id_usuario" class="border rounded-lg p-2 w-full"><option value="">Todos</option></select>
        </label>
        <label class="block">
          <span class="text-sm text-slate-700">Materia</span>
          <select id="id_materia" class="border rounded-lg p-2 w-full"><option value="">Todas</option></select>
        </label>
        <label class="block">
          <span class="text-sm text-slate-700">Grupo</span>
          <select id="id_grupo" class="border rounded-lg p-2 w-full"><option value="">Todos</option></select>
        </label>
        <label class="block">
          <span class="text-sm text-slate-700">Gestión</span>
          <select id="gestion" class="border rounded-lg p-2 w-full"><option value="">Todas</option></select>
        </label>
        <div class="md:col-span-2 flex gap-2">
          <button type="button" id="btn-generar" class="bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Generar</button>
          <button type="button" id="btn-csv" class="border border-slate-300 rounded-lg px-4 py-2">Exportar a Excel</button>
        </div>
      </form>
    </section>

    <section class="bg-white rounded-2xl shadow p-6 border border-slate-200">
      <h3 class="font-semibold text-slate-900 mb-2">Resumen por docente</h3>
      <div class="overflow-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50"><tr>
            <th class="p-2 text-left">Docente</th>
            <th class="p-2 text-left">Total</th>
            <th class="p-2 text-left">Presentes</th>
            <th class="p-2 text-left">Ausentes</th>
            <th class="p-2 text-left">Retrasos</th>
          </tr></thead>
          <tbody id="tb-doc" class="divide-y"></tbody>
        </table>
      </div>
    </section>

    <section class="bg-white rounded-2xl shadow p-6 border border-slate-200">
      <h3 class="font-semibold text-slate-900 mb-2">Detalle de asistencias</h3>
      <div class="overflow-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50"><tr>
            <th class="p-2 text-left">Fecha</th>
            <th class="p-2 text-left">Hora</th>
            <th class="p-2 text-left">Tipo</th>
            <th class="p-2 text-left">Docente</th>
            <th class="p-2 text-left">Materia</th>
            <th class="p-2 text-left">Grupo</th>
            <th class="p-2 text-left">Día</th>
            <th class="p-2 text-left">Rango</th>
            <th class="p-2 text-left">Aula</th>
          </tr></thead>
          <tbody id="tb-det" class="divide-y"></tbody>
        </table>
        <div id="msg" class="text-slate-500 text-sm mt-3"></div>
      </div>
    </section>
  </main>

  <script>
    const qs = s => document.querySelector(s)
    function setMsg(t){ qs('#msg').textContent = t||'' }
    async function getJson(url){ const r = await fetch(url, {headers:{'Accept':'application/json'}}); const ct=r.headers.get('content-type')||''; const d=ct.includes('application/json')? await r.json(): await r.text(); if(!r.ok) throw new Error(typeof d==='string'? d:(d.error||'Error')); return d }

    async function cargarOpciones(){
      try{
        const d = await getJson('/reportes/horarios-asistencia/opciones')
        const fillSel=(id,arr,lab)=>{ const el=qs(id); el.innerHTML='<option value="">'+lab+'</option>'; (arr||[]).forEach(x=>{ const o=document.createElement('option'); const v=x.id_usuario||x.id_materia||x.id_grupo||x; o.value=v; o.textContent = x.nombre || x.sigla? (x.sigla+' - '+x.nombre) : (x.id_grupo? x.nombre : v); el.appendChild(o) }) }
        fillSel('#id_usuario', d.docentes, 'Todos')
        fillSel('#id_materia', d.materias, 'Todas')
        fillSel('#id_grupo', d.grupos, 'Todos')
        const g = qs('#gestion'); g.innerHTML='<option value="">Todas</option>'; (d.gestiones||[]).forEach(x=>{ const o=document.createElement('option'); o.value=x; o.textContent=x; g.appendChild(o) })
      }catch(e){ /* opcional */ }
      // fechas por defecto: últimos 7 días
      const hoy = new Date(); const d7 = new Date(hoy.getTime()-6*86400000);
      const toYMD = (d)=> d.toISOString().slice(0,10);
      qs('#hasta').value = toYMD(hoy); qs('#desde').value = toYMD(d7);
    }

    function renderResumen(d){
      const tb = qs('#tb-doc'); tb.innerHTML=''
      const arr = (d.resumen && d.resumen.por_docente) ? d.resumen.por_docente : []
      arr.forEach(r=>{ tb.insertAdjacentHTML('beforeend', `<tr>
        <td class="p-2">${r.nombre||'-'}</td>
        <td class="p-2">${r.total||0}</td>
        <td class="p-2">${r.presentes||0}</td>
        <td class="p-2">${r.ausentes||0}</td>
        <td class="p-2">${r.retrasos||0}</td>
      </tr>`) })
    }

    function renderDetalle(d){
      const tb = qs('#tb-det'); tb.innerHTML=''
      const arr = Array.isArray(d.detalle)? d.detalle : []
      if(arr.length===0){ setMsg('Sin registros para el rango seleccionado.'); return }
      setMsg('')
      arr.forEach(r=>{ const aula = (r.nroaula? 'Aula '+r.nroaula : '-') + (r.id_modulo? (' · Módulo '+r.id_modulo):''); tb.insertAdjacentHTML('beforeend', `<tr>
        <td class="p-2">${r.fecha_registro}</td>
        <td class="p-2">${String(r.hora_registro||'').slice(0,5)}</td>
        <td class="p-2">${r.tipo||'-'}</td>
        <td class="p-2">${r.docente||'-'}</td>
        <td class="p-2">${(r.sigla||'')+' '+(r.materia||'')}</td>
        <td class="p-2">${r.grupo||'-'}</td>
        <td class="p-2">${r.dia||'-'}</td>
        <td class="p-2">${String(r.hora_ini||'').slice(0,5)} - ${String(r.hora_fin||'').slice(0,5)}</td>
        <td class="p-2">${aula}</td>
      </tr>`) })
    }

    async function generar(){
      setMsg('Generando...')
      const p = new URLSearchParams()
      ;['desde','hasta','id_usuario','id_materia','id_grupo','gestion'].forEach(id=>{ const v = qs('#'+id).value; if(v) p.set(id, v) })
      try{ const d = await getJson('/reportes/horarios-asistencia/data?'+p.toString()); window.__reporteHA = d; renderResumen(d); renderDetalle(d) }catch(e){ setMsg(e.message||'Error al generar') }
    }

    function descargarCSV(){
      const d = window.__reporteHA || {}
      const out = []
      // Usamos TSV + UTF-16LE para acentos correctos en Excel
      const porDoc = Array.isArray(d.resumen?.por_docente) ? d.resumen.por_docente : []
      out.push(['Resumen por docente'])
      out.push(['Docente','Total','Presentes','Ausentes','Retrasos'])
      porDoc.forEach(r=> out.push([r.nombre, r.total, r.presentes, r.ausentes, r.retrasos]))
      out.push([])
      const det = Array.isArray(d.detalle)? d.detalle : []
      out.push(['Detalle'])
      out.push(['Fecha','Hora','Tipo','Docente','Materia','Grupo','Día','Rango','Aula'])
      det.forEach(r=>{
        const aula = (r.nroaula? 'Aula '+r.nroaula : '-') + (r.id_modulo? (' · Módulo '+r.id_modulo):'')
        out.push([r.fecha_registro, String(r.hora_registro||'').slice(0,5), r.tipo, r.docente, (r.sigla? r.sigla+' ':'')+(r.materia||''), r.grupo, r.dia, String(r.hora_ini||'').slice(0,5)+' - '+String(r.hora_fin||'').slice(0,5), aula])
      })
      const tsv = out.map(row => Array.isArray(row) ? row.map(x=> String(x??'')).join('\t') : String(row)).join('\r\n')
      const toUTF16LE = (str) => { const buf=new Uint8Array(str.length*2+2); buf[0]=0xFF; buf[1]=0xFE; for(let i=0;i<str.length;i++){ const c=str.charCodeAt(i); buf[2+i*2]=c&0xFF; buf[3+i*2]=c>>8 } return buf }
      const blob = new Blob([toUTF16LE(tsv)], {type:'application/vnd.ms-excel;charset=utf-16le'})
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a'); a.href=url; a.download='reporte_horarios_asistencia.xls'; a.click(); URL.revokeObjectURL(url)
    }

    document.getElementById('btn-generar').addEventListener('click', generar)
    document.getElementById('btn-csv').addEventListener('click', descargarCSV)
    ;(async function init(){ await cargarOpciones(); await generar() })()
  </script>
</body>
</html>
