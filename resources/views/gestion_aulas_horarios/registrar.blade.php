<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrar aulas y capacidad</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50">
  <header class="max-w-6xl mx-auto px-6 py-5">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Registrar aulas y capacidad</h1>
        <p class="text-slate-600">Administra las aulas y sus capacidades</p>
      </div>
      <a href="/dashboard" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M15 18l-6-6 6-6"/></svg>
        Volver al perfil
      </a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-6 pb-16 space-y-6">
    <!-- Nueva Aula -->
    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200">
        <h2 class="font-semibold text-slate-900 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 12h18M12 3v18"/></svg>
          Nueva Aula
        </h2>
        <p class="text-slate-600">Completa los datos para registrar un aula nueva</p>
      </div>
      <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <label class="block">
          <span class="text-slate-800 font-medium">Nro Aula *</span>
          <input id="nroaula" type="number" min="1" placeholder="Ej: 101" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500" />
        </label>
        <label class="block">
          <span class="text-slate-800 font-medium">Capacidad *</span>
          <input id="capacidad" type="number" min="1" placeholder="Ej: 30" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500" />
        </label>
        <label class="block">
          <span class="text-slate-800 font-medium">Módulo *</span>
          <select id="id_modulo" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500">
            <option value="">Selecciona módulo</option>
          </select>
        </label>
        <label class="block">
          <span class="text-slate-800 font-medium">Tipo de aula (opcional)</span>
          <input id="tipo_aula" type="text" maxlength="30" placeholder="Ej: Teórica / Laboratorio" class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-sky-500 focus:border-sky-500" />
        </label>
        <label class="flex items-center gap-2">
          <input id="disponible" type="checkbox" checked class="rounded border-slate-300 text-sky-600" />
          <span class="text-slate-800">Disponible</span>
        </label>
      </div>
      <div class="p-6 pt-0">
        <button id="btnRegistrar" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg px-4 py-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 5v14m-7-7h14"/></svg>
          Registrar Aula
        </button>
        <span id="msg" class="ml-3 text-sm"></span>
      </div>
    </section>

    <!-- Lista -->
    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200">
        <h2 class="font-semibold text-slate-900">Aulas Registradas</h2>
        <p class="text-slate-600">Lista de aulas disponibles en el sistema</p>
      </div>
      <div class="p-6" id="lista">Cargando…</div>
    </section>

    
  </main>

  <script>
    const el = (id) => document.getElementById(id);
    const lista = el('lista');
    const msg = el('msg');

    function setDebug(_) { /* sin-op en front */ }

    async function loadModulos() {
      const res = await fetch('/api/asignacion/modulos', { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      setDebug({modulos:data});
      const sel = el('id_modulo');
      sel.innerHTML = '<option value="">Selecciona módulo</option>';
      (data.modulos || []).forEach(m => {
        const o = document.createElement('option');
        o.value = m.id_modulo; o.textContent = `${m.id_modulo} — ${m.facultad ?? ''}`;
        sel.appendChild(o);
      });
    }

    function tableRow(a) {
      const disp = a.disponible ? 'Sí' : 'No';
      return `<tr>
        <td class="py-2 px-3">${a.id_aula}</td>
        <td class="py-2 px-3">${a.nroaula}</td>
        <td class="py-2 px-3">${a.capacidad}</td>
        <td class="py-2 px-3">${a.tipo_aula ?? ''}</td>
        <td class="py-2 px-3">${a.id_modulo}</td>
        <td class="py-2 px-3">${disp}</td>
      </tr>`;
    }

    async function loadAulas() {
      const res = await fetch('/api/asignacion/aulas', { headers: { 'Accept':'application/json' } });
      const data = await res.json();
      setDebug({aulas:data});
      const items = data.aulas || [];
      if (items.length === 0) { lista.textContent = 'No hay aulas registradas o disponibles.'; return; }
      const head = `<thead><tr>
        <th class="text-left py-2 px-3">ID</th>
        <th class="text-left py-2 px-3">Nro Aula</th>
        <th class="text-left py-2 px-3">Capacidad</th>
        <th class="text-left py-2 px-3">Tipo</th>
        <th class="text-left py-2 px-3">Módulo</th>
        <th class="text-left py-2 px-3">Disponible</th>
      </tr></thead>`;
      const body = `<tbody>${items.map(tableRow).join('')}</tbody>`;
      lista.innerHTML = `<div class="overflow-auto"><table class="min-w-full">${head}${body}</table></div>`;
    }

    async function registrar() {
      msg.textContent = '';
      const payload = {
        nroaula: Number(el('nroaula').value),
        capacidad: Number(el('capacidad').value),
        id_modulo: Number(el('id_modulo').value),
        tipo_aula: el('tipo_aula').value || null,
        disponible: el('disponible').checked,
      };
      if (!payload.nroaula || !payload.capacidad || !payload.id_modulo) {
        msg.textContent = 'Complete los campos requeridos'; msg.className = 'text-red-600'; return;
      }
      const res = await fetch('/api/aulas/registrar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json().catch(() => ({status:res.status}));
      setDebug({registrar:{status:res.status, body:data}});
      if (!res.ok) { msg.textContent = data?.error || 'Error al registrar'; msg.className = 'text-red-600'; return; }
      msg.textContent = 'Aula registrada'; msg.className = 'text-green-600';
      el('nroaula').value = ''; el('capacidad').value = ''; el('tipo_aula').value = ''; el('disponible').checked = true;
      await loadAulas();
    }

    el('btnRegistrar').addEventListener('click', registrar);
    (async function init(){ await Promise.all([loadModulos(), loadAulas()]); })();
  </script>
</body>
</html>
