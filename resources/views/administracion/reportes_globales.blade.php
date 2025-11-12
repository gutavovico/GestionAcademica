<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reportes estadísticos globales</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <header class="max-w-7xl mx-auto mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Reportes estadísticos globales</h1>
      <p class="text-slate-600">Uso de aulas, carga docente y asistencia</p>
    </div>
    <a href="/dashboard" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">Volver al dashboard</a>
  </header>

  <main class="max-w-7xl mx-auto space-y-6">
    <!-- Filtros -->
    <section class="bg-white rounded-2xl shadow p-6 border border-slate-200">
      <h2 class="font-semibold text-slate-900 mb-4">Filtros</h2>
      <form id="filtros" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
        <label class="block md:col-span-2">
          <span class="text-sm text-slate-700">Gestión</span>
          <select id="gestion" class="border rounded-lg p-2 w-full">
            <option value="">Todas</option>
          </select>
        </label>
        <label class="block md:col-span-2">
          <span class="text-sm text-slate-700">Módulo</span>
          <select id="modulo" class="border rounded-lg p-2 w-full">
            <option value="">Todos</option>
          </select>
        </label>
        <div class="md:col-span-2 flex gap-2">
          <button type="button" id="btn-generar" class="bg-neutral-900 hover:bg-black text-white rounded-lg px-4 py-2">Generar reporte</button>
          <button type="button" id="btn-csv" class="border border-slate-300 rounded-lg px-4 py-2">Exportar a Excel</button>
        </div>
      </form>
    </section>

    <!-- Resumen general -->
    <section class="space-y-4">
      <h2 class="text-xl font-semibold text-slate-900">Resumen General</h2>
      <p class="text-slate-600">Vista general de asistencias y estadísticas</p>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-4 border border-slate-200">
          <div class="text-slate-600">Total Docentes</div>
          <div id="r_docentes_total" class="text-2xl font-semibold text-slate-900">-</div>
          <div class="text-xs text-slate-500 mt-1"><span id="r_docentes_con_carga">-</span> con carga</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border border-slate-200">
          <div class="text-slate-600">Presentes Hoy</div>
          <div id="r_presentes_hoy" class="text-2xl font-semibold text-emerald-700">-</div>
          <div class="text-xs text-slate-500 mt-1">Según registros de asistencia</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border border-slate-200">
          <div class="text-slate-600">Ausentes Hoy</div>
          <div id="r_ausentes_hoy" class="text-2xl font-semibold text-rose-700">-</div>
          <div class="text-xs text-slate-500 mt-1"><span id="r_retrasos_hoy">-</span> con retraso</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border border-slate-200">
          <div class="text-slate-600">Tasa de Asistencia</div>
          <div id="r_tasa_30d" class="text-2xl font-semibold text-indigo-700">-</div>
          <div class="text-xs text-slate-500 mt-1">Últimos 30 días</div>
        </div>
      </div>
    </section>

    <!-- Tendencia de asistencias -->
    <section class="bg-white rounded-2xl shadow p-6 border border-slate-200">
      <h3 class="font-semibold text-slate-900 mb-2">Tendencia de Asistencias</h3>
      <canvas id="chartAsis" height="120"></canvas>
    </section>

    <!-- Uso de aulas -->
    <section class="bg-white rounded-2xl shadow p-6 border border-slate-200">
      <h3 class="font-semibold text-slate-900 mb-2">Uso de aulas</h3>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
        <div class="rounded-lg border border-slate-200 p-3"><div class="text-slate-600">Total aulas</div><div id="a_total" class="text-xl font-semibold">-</div></div>
        <div class="rounded-lg border border-slate-200 p-3"><div class="text-slate-600">Disponibles</div><div id="a_disp" class="text-xl font-semibold">-</div></div>
        <div class="rounded-lg border border-slate-200 p-3"><div class="text-slate-600">Aulas ocupadas</div><div id="a_ocup" class="text-xl font-semibold">-</div></div>
        <div class="rounded-lg border border-slate-200 p-3"><div class="text-slate-600">Horarios activos</div><div id="h_act" class="text-xl font-semibold">-</div></div>
      </div>
      <div>
        <h4 class="font-medium text-slate-800 mb-2">Distribución por día</h4>
        <div id="graf_dias" class="space-y-2"></div>
      </div>
    </section>

    <!-- Asistencia y Top docentes -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="bg-white rounded-2xl shadow p-6 border border-slate-200">
        <h3 class="font-semibold text-slate-900 mb-2">Asistencia por tipo</h3>
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50"><tr><th class="p-2 text-left">Tipo</th><th class="p-2 text-left">Cantidad</th></tr></thead>
          <tbody id="tb_asistencia" class="divide-y"></tbody>
        </table>
      </div>
      <div class="bg-white rounded-2xl shadow p-6 border border-slate-200">
        <h3 class="font-semibold text-slate-900 mb-2">Top docentes por horas</h3>
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50"><tr><th class="p-2 text-left">Docente</th><th class="p-2 text-left">Horas</th></tr></thead>
          <tbody id="tb_top" class="divide-y"></tbody>
        </table>
      </div>
    </section>

    <div id="msg" class="max-w-7xl mx-auto text-slate-500 text-sm"></div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const qs = s => document.querySelector(s)
    function setMsg(t){ qs('#msg').textContent = t||'' }

    async function getJson(url){
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } })
      const ct = res.headers.get('content-type') || ''
      let data = ct.includes('application/json') ? await res.json() : await res.text()
      if(!res.ok){ throw new Error(typeof data==='string'? data : (data.error||'Error')) }
      return data
    }

    function bar(label, value, max){
      const pct = max>0? Math.round((value/max)*100) : 0
      return `<div><div class="flex justify-between text-sm text-slate-700"><span>${label}</span><span>${value}</span></div>
              <div class="h-2 bg-slate-100 rounded"><div class="h-2 bg-indigo-500 rounded" style="width:${pct}%"></div></div></div>`
    }

    let chart
    function fillResumen(d){
      // tarjetas
      qs('#r_docentes_total').textContent = d.resumen?.docentes_total ?? '-'
      qs('#r_presentes_hoy').textContent  = d.resumen?.presentes_hoy ?? '-'
      qs('#r_ausentes_hoy').textContent   = d.resumen?.ausentes_hoy ?? '-'
      qs('#r_retrasos_hoy').textContent   = d.resumen?.retrasos_hoy ?? '-'
      qs('#r_tasa_30d').textContent       = d.resumen?.tasa_asistencia_30d != null ? (d.resumen.tasa_asistencia_30d+'%') : '-'
      qs('#r_docentes_con_carga').textContent = d.carga_docente?.docentes_con_carga ?? '-'

      // uso de aulas
      qs('#a_total').textContent = d.aulas.total ?? '-'
      qs('#a_disp').textContent = d.aulas.disponibles ?? '-'
      qs('#a_ocup').textContent = d.aulas.ocupadas_distintas ?? '-'
      qs('#h_act').textContent = d.aulas.horarios_activos ?? '-'

      // barras por día
      const graf = qs('#graf_dias'); graf.innerHTML = ''
      const arr = Array.isArray(d.aulas.por_dia)? d.aulas.por_dia : []
      const max = arr.reduce((m,x)=> Math.max(m, Number(x.cantidad||0)), 0)
      arr.forEach(x=>{ graf.insertAdjacentHTML('beforeend', bar(x.dia||'-', Number(x.cantidad||0), max)) })

      // tablas
      const tbA = qs('#tb_asistencia'); tbA.innerHTML=''
      const tipos = Array.isArray(d.asistencia.por_tipo)? d.asistencia.por_tipo : []
      tipos.forEach(t=>{ tbA.insertAdjacentHTML('beforeend', `<tr><td class="p-2">${t.tipo||'-'}</td><td class="p-2">${t.cantidad||0}</td></tr>`) })

      const tbT = qs('#tb_top'); tbT.innerHTML=''
      const top = Array.isArray(d.carga_docente.top_docentes)? d.carga_docente.top_docentes : []
      top.forEach(r=>{ tbT.insertAdjacentHTML('beforeend', `<tr><td class="p-2">${r.nombre||'-'}</td><td class="p-2">${r.horas||0}</td></tr>`) })

      // chart tendencia
      const tend = Array.isArray(d.asistencia?.tendencia) ? d.asistencia.tendencia : []
      const labels = tend.map(x=> new Date(x.fecha).toLocaleDateString('es-BO', { day:'2-digit', month:'short'}))
      const presentes = tend.map(x=> Number(x.Presente||0))
      const ausentes  = tend.map(x=> Number(x.Ausente||0))
      const retrasos  = tend.map(x=> Number(x.Retraso||0))
      const ctx = document.getElementById('chartAsis').getContext('2d')
      if(chart){ chart.destroy() }
      chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [
            { label:'Presentes', data: presentes, borderColor:'#10b981', backgroundColor:'transparent', tension:.3 },
            { label:'Ausentes',  data: ausentes,  borderColor:'#ef4444', backgroundColor:'transparent', tension:.3 },
            { label:'Retrasos',  data: retrasos,  borderColor:'#f59e0b', backgroundColor:'transparent', tension:.3 },
          ]
        },
        options: { responsive: true, plugins: { legend: { position:'bottom' } }, scales: { y: { beginAtZero: true, precision:0 } } }
      })
    }

    async function cargarOpciones(){
      try{
        const data = await getJson('/reportes/globales/opciones')
        const selG = qs('#gestion'); selG.innerHTML = '<option value="">Todas</option>'
        ;(data.gestiones||[]).forEach(g=>{ const op=document.createElement('option'); op.value=g; op.textContent=g; selG.appendChild(op) })
        const selM = qs('#modulo'); selM.innerHTML = '<option value="">Todos</option>'
        ;(data.modulos||[]).forEach(m=>{ const op=document.createElement('option'); op.value=m.id_modulo; op.textContent=`${m.id_modulo} - ${m.facultad||''}`; selM.appendChild(op) })
      }catch(e){ setMsg(e.message||'No se pudieron cargar las opciones') }
    }

    async function generar(){
      setMsg('Generando...')
      const p = new URLSearchParams()
      const g = qs('#gestion').value; const m = qs('#modulo').value
      if(g) p.set('gestion', g)
      if(m) p.set('id_modulo', m)
      try{
        const data = await getJson('/reportes/globales/data?'+p.toString())
        window.__reporteGlobal = data
        fillResumen(data)
        setMsg('')
      }catch(e){ setMsg(e.message||'Error al generar reporte') }
    }

    function descargarCSV(){
      // Toma la última data generada para exportar todo el contenido
      const d = window.__reporteGlobal || {}
      const out = []
      // Usamos tabulador y UTF-16LE para compatibilidad total con Excel
      // (evita problemas de acentos y separadores regionales)
      out.push(['Resumen general'])
      out.push(['Métrica','Valor'])
      out.push(['Total Docentes', d.resumen?.docentes_total ?? ''])
      out.push(['Docentes con carga', d.carga_docente?.docentes_con_carga ?? ''])
      out.push(['Presentes Hoy', d.resumen?.presentes_hoy ?? ''])
      out.push(['Ausentes Hoy', d.resumen?.ausentes_hoy ?? ''])
      out.push(['Retrasos Hoy', d.resumen?.retrasos_hoy ?? ''])
      out.push(['Tasa asistencia 30d', d.resumen?.tasa_asistencia_30d ?? ''])
      out.push([])
      out.push(['Aulas'])
      out.push(['Total', d.aulas?.total ?? ''])
      out.push(['Disponibles', d.aulas?.disponibles ?? ''])
      out.push(['Aulas ocupadas distintas', d.aulas?.ocupadas_distintas ?? ''])
      out.push(['Horarios activos', d.aulas?.horarios_activos ?? ''])
      out.push([])
      const porDia = Array.isArray(d.aulas?.por_dia) ? d.aulas.por_dia : []
      if(porDia.length){ out.push(['Distribución por día']); out.push(['Día','Cantidad']); porDia.forEach(x=> out.push([x.dia, x.cantidad])) ; out.push([]) }
      const tend = Array.isArray(d.asistencia?.tendencia) ? d.asistencia.tendencia : []
      if(tend.length){ out.push(['Tendencia 14 días']); out.push(['Fecha','Presentes','Ausentes','Retrasos']); tend.forEach(x=> out.push([x.fecha, x.Presente, x.Ausente, x.Retraso])); out.push([]) }
      const tipos = Array.isArray(d.asistencia?.por_tipo) ? d.asistencia.por_tipo : []
      if(tipos.length){ out.push(['Asistencia por tipo']); out.push(['Tipo','Cantidad']); tipos.forEach(x=> out.push([x.tipo, x.cantidad])); out.push([]) }
      const top = Array.isArray(d.carga_docente?.top_docentes) ? d.carga_docente.top_docentes : []
      if(top.length){ out.push(['Top docentes por horas']); out.push(['Docente','Horas']); top.forEach(x=> out.push([x.nombre, x.horas])); out.push([]) }

      const tsv = out.map(row => Array.isArray(row) ? row.map(x=> String(x??'')).join('\t') : String(row)).join('\r\n')
      // Convertimos a UTF-16LE con BOM para Excel
      const toUTF16LE = (str) => {
        const buf = new Uint8Array(str.length*2 + 2)
        buf[0]=0xFF; buf[1]=0xFE; // BOM (LE)
        for(let i=0;i<str.length;i++){ const code=str.charCodeAt(i); buf[2+i*2]=code & 0xFF; buf[3+i*2]=code>>8 }
        return buf
      }
      const bytes = toUTF16LE(tsv)
      const blob = new Blob([bytes], {type:'application/vnd.ms-excel;charset=utf-16le'})
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a'); a.href = url; a.download = 'reporte_global.xls'; a.click(); URL.revokeObjectURL(url)
    }

    document.getElementById('btn-generar').addEventListener('click', generar)
    document.getElementById('btn-csv').addEventListener('click', descargarCSV)
    ;(async function init(){ await cargarOpciones(); await generar() })()
  </script>
</body>
</html>
