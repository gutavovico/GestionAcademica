<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestionar Horarios del Docente</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#eef5ff]">
  <header class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Gestionar Horarios del Docente</h1>
      <p class="text-slate-600">Consulta, crea y edita los horarios asignados</p>
    </div>
    <a href="/dashboard" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">← Volver al perfil</a>
  </header>

  <main class="max-w-6xl mx-auto px-6 pb-16 space-y-6">
    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/><path stroke-width="2" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        <h2 class="font-semibold text-slate-900">Seleccionar Docente</h2>
      </div>
      <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <label class="block md:col-span-2">
          <span class="text-slate-800 font-medium">Docente *</span>
          <select id="docente" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500"></select>
        </label>
        <div class="md:text-right">
          <button id="abrirCrear" class="inline-flex items-center gap-2 bg-black hover:bg-neutral-900 text-white rounded-lg px-4 py-2">+ Crear Horario</button>
        </div>
        <div id="docenteSel" class="md:col-span-3 bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-700">Docente seleccionado: —</div>
      </div>
    </section>

    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200 flex items-center gap-2 justify-between">
        <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 8v4l3 3"/></svg>
          <h2 class="font-semibold text-slate-900">Horarios Semanales</h2>
        </div>
        <span id="contador" class="text-slate-500 text-sm"></span>
      </div>
      <div class="p-6 overflow-auto">
        <table class="min-w-full" id="tabla">
          <thead><tr><th class="text-left px-3 py-2">Día</th><th class="text-left px-3 py-2">Hora Inicio</th><th class="text-left px-3 py-2">Hora Fin</th><th class="text-left px-3 py-2">Aula</th><th class="text-left px-3 py-2">Materia</th><th class="text-left px-3 py-2">Acciones</th></tr></thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
    </section>

    <div id="modal" class="fixed inset-0 bg-black/30 hidden items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
          <div>
            <h3 class="text-lg font-semibold">Crear Nuevo Horario</h3>
            <p id="modalDocente" class="text-slate-600 text-sm"></p>
          </div>
          <button id="cerrarModal" class="text-slate-500 hover:text-slate-700">✕</button>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
          <label class="block"><span class="text-slate-800 font-medium">Día *</span>
            <select id="c_dia" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500"><option>Lunes</option><option>Martes</option><option>Miércoles</option><option>Jueves</option><option>Viernes</option><option>Sábado</option><option>Domingo</option></select>
          </label>
          <label class="block"><span class="text-slate-800 font-medium">Aula *</span>
            <select id="c_aula" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500"></select>
          </label>
          <label class="block"><span class="text-slate-800 font-medium">Hora Inicio *</span>
            <input type="time" id="c_ini" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500" />
          </label>
          <label class="block"><span class="text-slate-800 font-medium">Hora Fin *</span>
            <input type="time" id="c_fin" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500" />
          </label>
          <label class="block md:col-span-2"><span class="text-slate-800 font-medium">Materia (carga horaria) *</span>
            <select id="c_carga" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500"></select>
          </label>
        </div>
        <div class="p-6 pt-0 flex items-center justify-end gap-2">
          <button id="cancelarCrear" class="border border-slate-200 rounded-lg px-4 py-2">Cancelar</button>
          <button id="crear" class="bg-black text-white rounded-lg px-4 py-2">+ Crear Horario</button>
        </div>
      </div>
    </div>

    <div id="toast" class="fixed bottom-4 right-4 hidden bg-black text-white rounded-lg px-3 py-2 text-sm">Hecho</div>
  </main>

  <script>
    const qs = (s) => document.querySelector(s);
    const qsa = (s) => document.querySelectorAll(s);
    const docenteSel = qs('#docente');
    const docenteTxt = qs('#docenteSel');
    const tbody = qs('#tbody');
    const contador = qs('#contador');
    const modal = qs('#modal');
    const toast = qs('#toast');

    function showToast(text){ toast.textContent = text; toast.classList.remove('hidden'); setTimeout(()=>toast.classList.add('hidden'), 1500); }
    function aulaLabel(a){ return `Aula ${a.nroaula} (Módulo ${a.id_modulo})`; }

    async function cargarDocentes(){
      const res = await fetch('/api/horarios/docentes', { headers:{'Accept':'application/json'} });
      const data = await res.json();
      docenteSel.innerHTML = '<option value="">Selecciona docente</option>';
      (data.docentes||[]).forEach(d=>{ const opt = document.createElement('option'); opt.value=d.id_usuario; const mat = d.materias ? ` — ${d.materias}` : ''; opt.textContent = `${d.nombre}${mat}`; docenteSel.appendChild(opt); });
    }
    async function cargarAulas(select){ const res = await fetch('/api/asignacion/aulas', { headers:{'Accept':'application/json'} }); const data = await res.json(); select.innerHTML = ''; (data.aulas||[]).forEach(a=>{ const o=document.createElement('option'); o.value=a.id_aula; o.textContent=aulaLabel(a); select.appendChild(o); }); }
    async function cargarCargas(idUsuario){ const res = await fetch('/api/horarios/cargas?id_usuario='+idUsuario, { headers:{'Accept':'application/json'} }); const data = await res.json(); const sel = qs('#c_carga'); sel.innerHTML=''; (data.cargas||[]).forEach(c=>{ const o=document.createElement('option'); o.value=c.id_carga; o.textContent = `${c.sigla || ''} ${c.materia || ''} — Grupo ${c.grupo || ''} — ${c.gestion}`; sel.appendChild(o); }); }

    async function listar(){ const idu = docenteSel.value; if(!idu){ tbody.innerHTML=''; contador.textContent=''; return; } const res = await fetch('/api/horarios?id_usuario='+idu, { headers:{'Accept':'application/json'} }); const data = await res.json(); const items = data.items||[]; contador.textContent = `${items.length} horarios`; if(items.length===0){ tbody.innerHTML = '<tr><td colspan="6" class="px-3 py-4 text-slate-500">Sin horarios</td></tr>'; return; } tbody.innerHTML = items.map(itemToRow).join(''); bindRowActions(); }

    function itemToRow(h){ const dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo']; const diaSel = `<select class="dia px-2 py-1 rounded border">${dias.map(d=>`<option ${h.dia===d?'selected':''}>${d}</option>`).join('')}</select>`; const hi = (h.hora_ini||'').toString().slice(0,5); const hf = (h.hora_fin||'').toString().slice(0,5); const aula = h.nroaula ? `Aula ${h.nroaula}` : h.id_aula; const aulaInput = `<input class="aula px-2 py-1 rounded border" value="${aula}" disabled title="Cambiar aula desde modal de creación (simplificado)"/>`; const materia = h.materia_sigla ? `${h.materia_sigla} - ${h.materia_nombre}` : (h.id_materia||''); return `<tr data-id="${h.id_horario}"><td class="px-3 py-2">${diaSel}</td><td class="px-3 py-2"><input type="time" class="ini px-2 py-1 rounded border" value="${hi}"></td><td class="px-3 py-2"><input type="time" class="fin px-2 py-1 rounded border" value="${hf}"></td><td class="px-3 py-2">${aulaInput}</td><td class="px-3 py-2">${materia}</td><td class="px-3 py-2 space-x-2"><button class="guardar text-sky-700" title="Guardar">💾</button><button class="eliminar text-red-600" title="Marcar asignado (DELETE)">🗑️</button></td></tr>`; }

function bindRowActions(){
  // Inicializar filas: ocultar guardar, deshabilitar inputs y agregar botón Editar
  qsa('#tbody tr').forEach(tr => {
    const dia = tr.querySelector('.dia');
    const ini = tr.querySelector('.ini');
    const fin = tr.querySelector('.fin');
    if (dia) dia.disabled = true; if (ini) ini.disabled = true; if (fin) fin.disabled = true;
    const guardar = tr.querySelector('.guardar');
    if (guardar) guardar.classList.add('hidden');
    const eliminar = tr.querySelector('.eliminar');
    if (eliminar) eliminar.setAttribute('title','Delete');
    // Insertar botón Editar si no existe
    if (!tr.querySelector('.editar') && guardar) {
      const editar = document.createElement('button');
      editar.className = 'editar text-slate-700';
      editar.title = 'Editar';
      editar.textContent = '✏️';
      guardar.parentNode.insertBefore(editar, guardar);
      editar.addEventListener('click', () => {
        if (dia) dia.disabled = false; if (ini) ini.disabled = false; if (fin) fin.disabled = false;
        editar.classList.add('hidden');
        guardar.classList.remove('hidden');
      });
    }
  });

  // Guardar cambios
  qsa('#tbody .guardar').forEach(btn => btn.addEventListener('click', async () => {
    const tr = btn.closest('tr'); const id = tr.dataset.id;
    const body = { dia: tr.querySelector('.dia').value, hora_ini: tr.querySelector('.ini').value, hora_fin: tr.querySelector('.fin').value };
    const res = await fetch('/api/horarios/'+id, {method:'PUT', headers:{'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify(body)});
    const data = await res.json(); if(!res.ok){ showToast(data.error||'Error al actualizar'); return; }
    showToast('Horario actualizado');
    // Volver a modo lectura
    const editar = tr.querySelector('.editar');
    if (editar) editar.classList.remove('hidden');
    btn.classList.add('hidden');
    tr.querySelector('.dia').disabled = true; tr.querySelector('.ini').disabled = true; tr.querySelector('.fin').disabled = true;
    await listar();
  }));

  // Eliminar (marcar asignado)
  qsa('#tbody .eliminar').forEach(btn => btn.addEventListener('click', async () => {
    const tr = btn.closest('tr'); const id = tr.dataset.id;
    const res = await fetch('/api/horarios/'+id, {method:'DELETE', headers:{'Accept':'application/json'}});
    const data = await res.json(); if(!res.ok){ showToast(data.error||'Error'); return; }
    showToast('Marcado como asignado'); await listar();
  }));
}

    function abrirModal(){ modal.classList.remove('hidden'); modal.classList.add('flex'); }
    function cerrarModal(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }
    qs('#abrirCrear').addEventListener('click', async ()=>{ const idu = docenteSel.value; if(!idu){ showToast('Selecciona docente'); return; } qs('#modalDocente').textContent = 'Agrega un nuevo horario para ' + docenteSel.selectedOptions[0].textContent; await cargarAulas(qs('#c_aula')); await cargarCargas(idu); abrirModal(); });
    qs('#cerrarModal').addEventListener('click', cerrarModal); qs('#cancelarCrear').addEventListener('click', cerrarModal);
    qs('#crear').addEventListener('click', async ()=>{ const idu = docenteSel.value; if(!idu){ showToast('Selecciona docente'); return; } const body = { id_carga: Number(qs('#c_carga').value), id_aula: Number(qs('#c_aula').value), dia: qs('#c_dia').value, hora_ini: qs('#c_ini').value, hora_fin: qs('#c_fin').value }; const res = await fetch('/api/horarios', { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify(body) }); const data = await res.json(); if(!res.ok){ showToast(data.error||'Error al crear'); return; } cerrarModal(); showToast('Horario creado'); await listar(); });

    docenteSel.addEventListener('change', ()=>{ docenteTxt.textContent = docenteSel.value ? ('Docente seleccionado: ' + docenteSel.selectedOptions[0].textContent) : 'Docente seleccionado: —'; listar(); });
    (async function init(){ await cargarDocentes(); listar(); })();
  </script>
</body>
</html>

