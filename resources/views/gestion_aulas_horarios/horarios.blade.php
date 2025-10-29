<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>CU11 - Gestionar Horarios del Docente</title>
    <style>
        :root { color-scheme: light dark; }
        body { font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; margin: 2rem; line-height: 1.45; }
        h1 { margin-bottom: .5rem; }
        .hint { color:#64748b; margin-bottom: 1rem; font-size: .95rem; }
        .grid { display:grid; gap:.75rem; }
        .row { display:grid; grid-template-columns: repeat(3, 1fr); gap:.75rem; }
        label { display:grid; gap:.35rem; }
        input[type="number"], input[type="text"], input[type="time"], select { padding:.55rem .65rem; border:1px solid #cbd5e1; border-radius:.375rem; }
        button { padding:.55rem 1rem; border-radius:.375rem; border:1px solid #0ea5e9; background:#0ea5e9; color:#fff; cursor:pointer; }
        button.secondary { border-color:#cbd5e1; background:transparent; color:inherit; }
        button.warn { border-color:#f59e0b; background:#f59e0b; }
        .actions { display:flex; gap:.5rem; align-items:center; margin:.5rem 0; }
        table { border-collapse:collapse; width:100%; margin-top:1rem; }
        th, td { border:1px solid #e2e8f0; padding:.45rem .55rem; text-align:left; }
        th { background:#f1f5f9; }
        .right { text-align:right; }
        pre { background:#0b1020; color:#eaf2ff; padding:1rem; border-radius:.5rem; overflow:auto; }
        .ok { color:#16a34a; }
        .err { color:#dc2626; }
        .muted { color:#94a3b8; }
        .small { font-size:.9rem; }
        .pill { display:inline-block; padding:.1rem .4rem; border-radius:.5rem; background:#e2e8f0; color:#111827; }
    </style>
</head>
<body>
    <h1>Gestionar Horarios del Docente</h1>
    <p class="hint">CRUD completo sobre la tabla <em>horario</em>. Eliminar marca estado="Activo".</p>

    <section class="grid" id="filtros">
        <div class="row">
            <label>
                <span>Docente (id_usuario, opcional)</span>
                <input type="number" id="f_id_usuario" min="1" placeholder="Filtrar por docente">
            </label>
            <label>
                <span>Día (opcional)</span>
                <select id="f_dia">
                    <option value="">— Cualquiera —</option>
                    <option>Lunes</option>
                    <option>Martes</option>
                    <option>Miércoles</option>
                    <option>Jueves</option>
                    <option>Viernes</option>
                    <option>Sábado</option>
                    <option>Domingo</option>
                </select>
            </label>
            <label>
                <span>Estado (opcional)</span>
                <select id="f_estado">
                    <option value="">— Cualquiera —</option>
                    <option>Activo</option>
                    <option>Inactivo</option>
                </select>
            </label>
        </div>
        <div class="actions">
            <button id="btnBuscar">Buscar</button>
            <button class="secondary" id="btnLimpiarFiltros">Limpiar filtros</button>
        </div>
    </section>

    <section class="grid" id="formulario" style="margin-top:1.25rem;">
        <h2 class="small">Crear/Editar</h2>
        <div class="row">
            <label>
                <span>ID Horario (solo edición)</span>
                <input type="number" id="h_id" placeholder="Auto" disabled>
            </label>
            <label>
                <span>ID Carga (opcional)</span>
                <input type="number" id="h_id_carga" min="1" placeholder="NULL si vacío">
            </label>
            <label>
                <span>Aula</span>
                <select id="h_id_aula">
                    <option value="">— Selecciona aula —</option>
                </select>
            </label>
        </div>
        <div class="row">
            <label>
                <span>Día</span>
                <select id="h_dia">
                    <option>Lunes</option>
                    <option>Martes</option>
                    <option>Miércoles</option>
                    <option>Jueves</option>
                    <option>Viernes</option>
                    <option>Sábado</option>
                    <option>Domingo</option>
                </select>
            </label>
            <label>
                <span>Hora inicio</span>
                <input type="time" id="h_hora_ini">
            </label>
            <label>
                <span>Hora fin</span>
                <input type="time" id="h_hora_fin">
            </label>
        </div>
        <div class="row">
            <label>
                <span>Estado</span>
                <select id="h_estado">
                    <option>Activo</option>
                    <option>Inactivo</option>
                </select>
            </label>
        </div>
        <div class="actions">
            <button id="btnCrear">Crear</button>
            <button id="btnActualizar" class="secondary">Actualizar</button>
            <button id="btnMarcarAsignado" class="warn">Marcar Asignado (DELETE)</button>
            <button id="btnLimpiarForm" class="secondary">Limpiar</button>
        </div>
    </section>

    <section style="margin-top:1rem;">
        <h2 class="small">Listado</h2>
        <div id="tabla">Sin datos aún.</div>
    </section>

    <h2 class="small">Debug</h2>
    <pre id="debug">Debug no disponible</pre>

    <script>
        const debug = document.getElementById('debug');
        const tabla = document.getElementById('tabla');
        const selAula = document.getElementById('h_id_aula');

        function setDebug(obj) {
            try { debug.textContent = JSON.stringify(obj, null, 2); } catch (_) { debug.textContent = String(obj); }
        }

        async function cargarAulas() {
            const res = await fetch('/api/asignacion/aulas', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            setDebug({aulas:data});
            selAula.innerHTML = '<option value="">— Selecciona aula —</option>';
            (data.aulas || []).forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id_aula;
                opt.textContent = `Aula ${a.nroaula} (cap ${a.capacidad}) - Módulo ${a.id_modulo}`;
                selAula.appendChild(opt);
            });
        }

        function row(h) {
            const estadoPill = `<span class="pill">${h.estado}</span>`;
            const aula = h.nroaula ? `Aula ${h.nroaula}` : h.id_aula;
            return `<tr data-id="${h.id_horario}">
                <td>${h.id_horario}</td>
                <td>${h.dia}</td>
                <td>${h.hora_ini?.toString().slice(0,5)}</td>
                <td>${h.hora_fin?.toString().slice(0,5)}</td>
                <td>${estadoPill}</td>
                <td>${aula}</td>
                <td>${h.id_carga ?? ''}</td>
                <td class="right"><button class="secondary btn-editar">Editar</button></td>
            </tr>`;
        }

        async function buscar() {
            const params = new URLSearchParams();
            const idu = document.getElementById('f_id_usuario').value;
            const dia = document.getElementById('f_dia').value;
            const est = document.getElementById('f_estado').value;
            if (idu) params.set('id_usuario', idu);
            if (dia) params.set('dia', dia);
            if (est) params.set('estado', est);
            const res = await fetch('/api/horarios?' + params.toString(), { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            setDebug({list:data});
            const items = data.items || [];
            if (items.length === 0) { tabla.textContent = 'Sin resultados'; return; }
            const head = `<thead><tr><th>ID</th><th>Día</th><th>Inicio</th><th>Fin</th><th>Estado</th><th>Aula</th><th>ID Carga</th><th></th></tr></thead>`;
            const body = `<tbody>${items.map(row).join('')}</tbody>`;
            tabla.innerHTML = `<table>${head}${body}</table>`;
            tabla.querySelectorAll('.btn-editar').forEach(btn => btn.addEventListener('click', () => {
                const tr = btn.closest('tr');
                const id = Number(tr.dataset.id);
                editar(id);
            }));
        }

        async function editar(id) {
            const res = await fetch(`/api/horarios/${id}`, { headers: { 'Accept':'application/json' } });
            const data = await res.json();
            setDebug({show:data});
            const h = data.horario; if (!h) return;
            document.getElementById('h_id').value = h.id_horario;
            document.getElementById('h_id_carga').value = h.id_carga ?? '';
            document.getElementById('h_dia').value = h.dia;
            document.getElementById('h_hora_ini').value = (h.hora_ini||'').toString().slice(0,5);
            document.getElementById('h_hora_fin').value = (h.hora_fin||'').toString().slice(0,5);
            document.getElementById('h_estado').value = h.estado;
            if ([...selAula.options].some(o => Number(o.value) === Number(h.id_aula))) selAula.value = h.id_aula;
        }

        function payloadFromForm(includeIdCarga = true) {
            const d = {
                id_aula: Number(selAula.value),
                dia: document.getElementById('h_dia').value,
                hora_ini: document.getElementById('h_hora_ini').value,
                hora_fin: document.getElementById('h_hora_fin').value,
                estado: document.getElementById('h_estado').value,
            };
            const idc = document.getElementById('h_id_carga').value;
            if (includeIdCarga && idc) d.id_carga = Number(idc);
            return d;
        }

        async function crear() {
            const body = payloadFromForm(true);
            const res = await fetch('/api/horarios', { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify(body) });
            const data = await res.json(); setDebug({create:data});
            if (!res.ok) return alert(data.error||'Error al crear');
            limpiarForm(); await buscar();
        }

        async function actualizar() {
            const id = Number(document.getElementById('h_id').value || 0);
            if (!id) return alert('Selecciona un registro de la tabla');
            const body = payloadFromForm(true);
            const res = await fetch(`/api/horarios/${id}`, { method:'PUT', headers:{'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify(body) });
            const data = await res.json(); setDebug({update:data});
            if (!res.ok) return alert(data.error||'Error al actualizar');
            limpiarForm(); await buscar();
        }

        async function marcarAsignado() {
            const id = Number(document.getElementById('h_id').value || 0);
            if (!id) return alert('Selecciona un registro de la tabla');
            const res = await fetch(`/api/horarios/${id}`, { method:'DELETE', headers:{'Accept':'application/json'} });
            const data = await res.json(); setDebug({delete:data});
            if (!res.ok) return alert(data.error||'Error al marcar asignado');
            limpiarForm(); await buscar();
        }

        function limpiarForm() {
            document.getElementById('h_id').value = '';
            document.getElementById('h_id_carga').value = '';
            selAula.value = '';
            document.getElementById('h_dia').value = 'Lunes';
            document.getElementById('h_hora_ini').value = '';
            document.getElementById('h_hora_fin').value = '';
            document.getElementById('h_estado').value = 'Activo';
        }

        document.getElementById('btnBuscar').addEventListener('click', buscar);
        document.getElementById('btnLimpiarFiltros').addEventListener('click', () => { document.getElementById('f_id_usuario').value=''; document.getElementById('f_dia').value=''; document.getElementById('f_estado').value=''; buscar(); });
        document.getElementById('btnCrear').addEventListener('click', crear);
        document.getElementById('btnActualizar').addEventListener('click', actualizar);
        document.getElementById('btnMarcarAsignado').addEventListener('click', marcarAsignado);
        document.getElementById('btnLimpiarForm').addEventListener('click', limpiarForm);

        (async function init(){ await cargarAulas(); await buscar(); })();
    </script>
</body>
</html>

