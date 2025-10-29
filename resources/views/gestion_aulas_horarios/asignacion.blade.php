<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>CU8 - Asignar Aula a Grupo</title>
    <style>
        :root { color-scheme: light dark; }
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 2rem; line-height: 1.45; }
        h1 { margin-bottom: .25rem; }
        .hint { color: #64748b; margin-bottom: 1rem; font-size: .95rem; }
        form { display: grid; gap: .75rem; max-width: 800px; }
        .row { display: grid; grid-template-columns: repeat(3, 1fr); gap: .75rem; }
        label { display: grid; gap: .35rem; }
        input[type="number"], input[type="text"] { padding: .55rem .65rem; border: 1px solid #cbd5e1; border-radius: .375rem; }
        .actions { display: flex; gap: .5rem; align-items: center; }
        button { padding: .55rem 1rem; border-radius: .375rem; border: 1px solid #0ea5e9; background: #0ea5e9; color: white; cursor: pointer; }
        button.secondary { border-color: #cbd5e1; background: transparent; color: inherit; }
        table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
        th, td { border: 1px solid #e2e8f0; padding: .5rem .6rem; text-align: left; }
        th { background: #f1f5f9; }
        .right { text-align: right; }
        pre { background: #0b1020; color: #eaf2ff; padding: 1rem; border-radius: .5rem; overflow: auto; }
        .ok { color: #16a34a; }
        .err { color: #dc2626; }
        .muted { color: #94a3b8; }
        .slot { display:inline-flex; align-items:center; gap:.35rem; margin:.25rem .35rem; padding:.2rem .35rem; border-radius:.375rem; }
        .slot.busy { opacity:.55; text-decoration: line-through; }
        .slot.free { background: rgba(14,165,233,.15); border: 1px solid rgba(14,165,233,.35); }
        .hidden { display: none; }
    </style>
    </head>
<body>
    <h1>Asignación de Aula a Grupo</h1>
    <p class="hint">Selecciona un aula y un día; verás los horarios de 90 minutos disponibles entre 07:00 y 22:30 (se omiten reservas Activas). Luego asigna el horario a una carga horaria.</p>

    <form id="filtros">
        <div class="row">
            <label>
                <span>ID Carga Horaria (opcional)</span>
                <input type="number" name="id_carga" min="1" placeholder="Dejar vacío si aún no asignada">
            </label>
        </div>

        <div class="row">
            <label>
                <span>Día</span>
                <input type="text" name="dia" placeholder="Ej: Lunes" required>
            </label>
        </div>

        <div class="row">
            <label>
                <span>Capacidad mínima (filtro aulas)</span>
                <input type="number" name="capacidad" min="1" value="1">
            </label>
            <label>
                <span>ID Módulo (filtro aulas)</span>
                <input type="number" name="id_modulo" min="1" placeholder="Filtrar por módulo">
            </label>
            <label>
                <span>Tipo de aula (filtro aulas)</span>
                <input type="text" name="tipo_aula" maxlength="30" placeholder="Filtrar por tipo">
            </label>
        </div>

        <div class="row">
            <label>
                <span>Aula</span>
                <select name="id_aula" id="id_aula">
                    <option value="">— Selecciona aula —</option>
                </select>
            </label>
        </div>

        <div class="actions">
            <button type="button" id="btnBuscarAulas">Buscar aulas</button>
            <button type="button" id="btnBuscarSlots">Buscar horarios</button>
            <button class="secondary" type="button" id="btnEjemplo">Rellenar ejemplo</button>
            <button class="secondary" type="button" id="btnLimpiar">Limpiar</button>
        </div>
    </form>

    <div id="resultados" class="muted">Sin resultados aún. Usa "Buscar aulas" y luego "Buscar horarios".</div>
    <div id="mensaje"></div>
    <pre id="debug" class="muted">Debug no disponible</pre>

    <script>
        const form = document.getElementById('filtros');
        const resultados = document.getElementById('resultados');
        const msg = document.getElementById('mensaje');
        const debug = document.getElementById('debug');
        const btnEjemplo = document.getElementById('btnEjemplo');
        const btnLimpiar = document.getElementById('btnLimpiar');

        function cleanMsg() { msg.textContent = ''; msg.className = ''; }
        function showOk(t) { msg.textContent = t; msg.className = 'ok'; }
        function showErr(t) { msg.textContent = t; msg.className = 'err'; }

        btnEjemplo.addEventListener('click', () => {
            form.id_carga.value = 1;
            form.dia.value = 'Lunes';
            form.capacidad.value = 30;
            form.id_modulo.value = 236;
            form.tipo_aula.value = '';
            cleanMsg();
        });

        btnLimpiar.addEventListener('click', () => {
            form.reset();
            resultados.innerHTML = '<span class="muted">Sin resultados aún. Usa "Buscar aulas".</span>';
            debug.textContent = 'Debug no disponible';
            cleanMsg();
        });

        // Buscar aulas para el select
        document.getElementById('btnBuscarAulas').addEventListener('click', async () => {
            cleanMsg();
            const fd = new FormData(form);
            const params = new URLSearchParams();
            if (fd.get('capacidad')) params.set('capacidad', fd.get('capacidad'));
            if (fd.get('id_modulo')) params.set('id_modulo', fd.get('id_modulo'));
            if (fd.get('tipo_aula')) params.set('tipo_aula', fd.get('tipo_aula'));

            try {
                const res = await fetch(`/api/asignacion/aulas?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                debug.textContent = JSON.stringify({ status: res.status, body: data }, null, 2);

                const sel = document.getElementById('id_aula');
                sel.innerHTML = '<option value="">— Selecciona aula —</option>';
                (data.aulas || []).forEach(a => {
                    const opt = document.createElement('option');
                    opt.value = a.id_aula;
                    opt.textContent = `Aula ${a.nroaula} (cap ${a.capacidad}) - Módulo ${a.id_modulo}`;
                    sel.appendChild(opt);
                });

                showOk('Aulas cargadas');
            } catch (err) {
                showErr('No se pudieron cargar aulas: ' + err.message);
            }
        });

        // Buscar slots disponibles del aula seleccionada
        async function buscarSlots() {
            cleanMsg();
            resultados.textContent = 'Buscando horarios…';
            const selAula = document.getElementById('id_aula');
            const idAula = selAula && selAula.value ? selAula.value : '';
            if (!idAula) { showErr('Selecciona un aula'); resultados.textContent=''; return; }
            if (!form.dia.value) { showErr('Selecciona un día'); resultados.textContent=''; return; }
            const fd = new FormData(form);
            const params = new URLSearchParams({ dia: fd.get('dia'), id_aula: String(idAula) });
            try {
                const res = await fetch(`/api/asignacion/slots?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                debug.textContent = JSON.stringify({ status: res.status, body: data }, null, 2);
                if (!res.ok) { showErr(data?.error || 'Error consultando slots'); resultados.textContent=''; return; }
                renderSlots(data.slots || []);
            } catch (err) {
                resultados.textContent = '';
                showErr('Error de red al consultar slots: ' + err.message);
            }
        }

        document.getElementById('btnBuscarSlots').addEventListener('click', buscarSlots);

        // Al seleccionar aula o cambiar el día, refrescar slots automáticamente
        document.getElementById('id_aula').addEventListener('change', () => {
            if (form.dia.value) buscarSlots();
        });
        form.dia.addEventListener('change', () => {
            if (form.id_aula.value) buscarSlots();
        });

        function renderSlots(slots) {
            if (slots.length === 0) {
                resultados.innerHTML = '<span class="muted">No hay horarios disponibles para esa aula y día.</span>';
                return;
            }
            const buttons = slots.map((s) => {
                const ocupado = !!s.ocupado;
                const cls = 'slot ' + (ocupado ? 'busy' : 'free');
                const disabled = ocupado ? 'disabled' : '';
                const title = ocupado ? 'Ocupado (Activo)' : 'Libre';
                return `
                    <label class="${cls}" title="${title}">
                        <input type="radio" name="slot" value="${s.hora_ini}|${s.hora_fin}" ${disabled}> ${s.hora_ini}–${s.hora_fin}
                    </label>
                `;
            }).join('');
            resultados.innerHTML = `
                <div>
                    <strong>Horarios (Libre/Ocupado):</strong>
                    <div>${buttons}</div>
                </div>
                <div class="actions" style="margin-top:.75rem;">
                    <button type="button" id="btnAsignar">Asignar horario</button>
                </div>
            `;
            document.getElementById('btnAsignar').addEventListener('click', asignarDesdeSeleccion);
        }

        async function asignarDesdeSeleccion() {
            cleanMsg();
            const selAula = document.getElementById('id_aula');
            const idAula = selAula && selAula.value ? parseInt(selAula.value, 10) : null;
            const fd = new FormData(form);
            if (!idAula) { showErr('Selecciona un aula'); return; }
            const slotSel = (document.querySelector('input[name="slot"]:checked') || {}).value;
            if (!slotSel) { showErr('Selecciona un horario'); return; }
            const [horaIni, horaFin] = slotSel.split('|');
            showOk(`Asignando aula ${idAula} en ${horaIni}–${horaFin}…`);
            const rawIdCarga = fd.get('id_carga');
            const payload = {
                dia: fd.get('dia'),
                hora_ini: horaIni,
                hora_fin: horaFin,
                id_aula: idAula,
            };
            if (rawIdCarga) payload.id_carga = Number(rawIdCarga);

            try {
                const res = await fetch('/api/asignacion/asignar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json().catch(() => ({ status: res.status, text: 'No JSON' }));
                debug.textContent = JSON.stringify({ status: res.status, body: data }, null, 2);

                if (!res.ok) {
                    showErr(data?.error || 'No se pudo asignar');
                    return;
                }

                showOk((data?.ok || 'OK') + (data?.horario ? ` (ID Horario: ${data.horario.id_horario ?? ''})` : ''));
            } catch (err) {
                showErr('Error de red al asignar: ' + err.message);
            }
        }
    </script>
</body>
</html>
