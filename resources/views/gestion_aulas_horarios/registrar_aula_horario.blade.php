<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrar Aula a Horario</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#eef5ff]">
  <header class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Registrar Aula a Horario</h1>
      <p class="text-slate-600">Asigna un aula a un horario específico</p>
    </div>
    <a href="/dashboard" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M15 18l-6-6 6-6"/></svg>
      Volver al perfil
    </a>
  </header>

  <main class="max-w-6xl mx-auto px-6 pb-16 space-y-6">
    <!-- Selección Aula/Día -->
    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z"/></svg>
        <h2 class="font-semibold text-slate-900">Seleccionar Aula y Día</h2>
      </div>
      <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-slate-800 font-medium">Aula *</span>
          <select id="aula" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500">
            <option value="">Selecciona aula</option>
          </select>
        </label>
        <label class="block">
          <span class="text-slate-800 font-medium">Día de la semana *</span>
          <select id="dia" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500">
            <option>Lunes</option>
            <option>Martes</option>
            <option>Miércoles</option>
            <option>Jueves</option>
            <option>Viernes</option>
            <option>Sábado</option>
            <option>Domingo</option>
          </select>
        </label>
        <div class="md:col-span-2 bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-700" id="detAula">Aula seleccionada: —</div>
        <div class="md:col-span-2">
          <button id="btnBuscar" class="inline-flex items-center gap-2 bg-black hover:bg-neutral-900 text-white rounded-lg px-4 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/></svg>
            Buscar Horarios Disponibles
          </button>
        </div>
      </div>
    </section>

    <!-- Horarios Disponibles -->
    <section class="bg-white rounded-2xl shadow border border-slate-200" id="secSlots">
      <div class="p-6 border-b border-slate-200 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 8v4l3 3"/></svg>
        <h2 class="font-semibold text-slate-900">Horarios Disponibles (Intervalos de 1.5 horas)</h2>
      </div>
      <div class="p-6">
        <p class="text-slate-600 mb-3">Selecciona un horario disponible o usa el horario personalizado.</p>
        <div id="slots" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3"></div>
      </div>
    </section>

    <!-- Horario Personalizado -->
    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 8v4l3 3"/></svg>
        <h2 class="font-semibold text-slate-900">Horario Personalizado</h2>
      </div>
      <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-slate-800 font-medium">Hora de Inicio *</span>
          <input id="hIni" type="time" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500" />
        </label>
        <label class="block">
          <span class="text-slate-800 font-medium">Hora de Fin *</span>
          <input id="hFin" type="time" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500" />
        </label>
        <div class="md:col-span-2">
          <button id="btnPersonalizado" class="inline-flex items-center gap-2 bg-black hover:bg-neutral-900 text-white rounded-lg px-4 py-2">Registrar Horario Personalizado</button>
        </div>
      </div>
    </section>

    <div id="msg" class="text-sm"></div>
  </main>

  <script>
    const qs = (s) => document.querySelector(s);
    const aulaSel = qs('#aula');
    const diaSel = qs('#dia');
    const detAula = qs('#detAula');
    const slotsDiv = qs('#slots');
    const msg = qs('#msg');

    function setMsg(text, ok=true){
      msg.textContent = text || '';
      msg.className = 'text-sm ' + (ok ? 'text-green-600' : 'text-red-600');
    }

    function aulaOptionText(a){
      return `Aula ${a.nroaula} - Módulo ${a.id_modulo} (Cap: ${a.capacidad})`;
    }

    async function cargarAulas(){
      const res = await fetch('/api/asignacion/aulas', { headers:{'Accept':'application/json'} });
      const data = await res.json();
      aulaSel.innerHTML = '<option value="">Selecciona aula</option>';
      (data.aulas||[]).forEach(a=>{
        const opt = document.createElement('option');
        opt.value = a.id_aula; opt.textContent = aulaOptionText(a);
        opt.dataset.capacidad = a.capacidad;
        opt.dataset.modulo = a.id_modulo;
        opt.dataset.nroaula = a.nroaula;
        aulaSel.appendChild(opt);
      });
    }

    function actualizarDetalleAula(){
      const id = aulaSel.value; if(!id){ detAula.textContent = 'Aula seleccionada: —'; return; }
      const opt = aulaSel.selectedOptions[0];
      detAula.innerHTML = `Aula seleccionada: <strong>Aula ${opt.dataset.nroaula}</strong> - Módulo <strong>${opt.dataset.modulo}</strong> — <span class="px-2 py-0.5 text-sm rounded bg-slate-100 border">Capacidad: ${opt.dataset.capacidad}</span>`;
    }

    function cardSlot(item){
      const ocupado = !!item.ocupado;
      const base = 'rounded-xl px-5 py-6 text-center border transition';
      const cls = ocupado ? 'bg-slate-100 border-slate-200 text-slate-400' : 'bg-sky-50 border-sky-200 text-slate-800 hover:bg-sky-100 cursor-pointer';
      const info = ocupado ? '<span class="mt-2 inline-block text-xs px-2 py-0.5 rounded bg-slate-200">Ocupado</span>' : '';
      return `<div class="${base} ${cls}" data-ini="${item.hora_ini}" data-fin="${item.hora_fin}">
        <div class="font-semibold text-lg">${item.hora_ini}</div>
        <div class="text-slate-500">a</div>
        <div class="font-semibold text-lg">${item.hora_fin}</div>
        ${info}
      </div>`;
    }

    function renderSlots(items){
      if(!items || items.length===0){ slotsDiv.innerHTML = '<div class="text-slate-500">No hay horarios para mostrar</div>'; return; }
      slotsDiv.innerHTML = items.map(cardSlot).join('');
      slotsDiv.querySelectorAll('div[data-ini]')
        .forEach(card=>{
          if(card.querySelector('.bg-slate-200')) return; // ocupado
          card.addEventListener('click', ()=> asignar(card.dataset.ini, card.dataset.fin));
        });
    }

    async function buscar(){
      setMsg('');
      if(!aulaSel.value){ setMsg('Selecciona un aula', false); return; }
      const url = `/api/asignacion/slots?dia=${encodeURIComponent(diaSel.value)}&id_aula=${encodeURIComponent(aulaSel.value)}`;
      const res = await fetch(url, { headers:{'Accept':'application/json'} });
      const data = await res.json();
      renderSlots(data.slots||[]);
    }

    async function asignar(hora_ini, hora_fin){
      setMsg(`Asignando ${hora_ini}–${hora_fin}…`);
      const body = { id_aula: Number(aulaSel.value), dia: diaSel.value, hora_ini, hora_fin };
      const res = await fetch('/api/asignacion/asignar', { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify(body) });
      const data = await res.json().catch(()=>({}));
      if(!res.ok){ setMsg(data?.error||'No se pudo asignar', false); return; }
      setMsg('Horario creado');
      await buscar();
    }

    async function asignarPersonalizado(){
      if(!aulaSel.value){ setMsg('Selecciona un aula', false); return; }
      const ini = qs('#hIni').value; const fin = qs('#hFin').value;
      if(!ini || !fin){ setMsg('Completa las horas', false); return; }
      await asignar(ini, fin);
    }

    aulaSel.addEventListener('change', actualizarDetalleAula);
    qs('#btnBuscar').addEventListener('click', buscar);
    qs('#btnPersonalizado').addEventListener('click', asignarPersonalizado);

    (async function init(){ await cargarAulas(); actualizarDetalleAula(); })();
  </script>
</body>
</html>

