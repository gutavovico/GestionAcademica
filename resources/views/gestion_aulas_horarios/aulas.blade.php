<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Gestión Aulas Horarios – Prueba de Endpoint</title>
    <style>
        :root { color-scheme: light dark; }
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 2rem; line-height: 1.4; }
        h1 { margin-bottom: 1rem; }
        form { max-width: 560px; display: grid; gap: .75rem; }
        label { display: grid; gap: .35rem; }
        input[type="text"], input[type="number"] { padding: .5rem .6rem; border: 1px solid #cbd5e1; border-radius: .375rem; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
        .actions { display: flex; gap: .5rem; align-items: center; }
        button { padding: .55rem 1rem; border-radius: .375rem; border: 1px solid #0ea5e9; background: #0ea5e9; color: white; cursor: pointer; }
        button.secondary { border-color: #cbd5e1; background: transparent; color: inherit; }
        pre { background: #0b1020; color: #eaf2ff; padding: 1rem; border-radius: .5rem; overflow: auto; }
        .hint { font-size: .9rem; color: #64748b; }
    </style>
    </head>
<body>
    <h1>Probar: POST /api/aulas/registrar</h1>
    <p class="hint">Rellena los datos del aula y envía la solicitud al endpoint. Los campos deben cumplir con las validaciones del backend.</p>

    <form id="aulaForm">
        <div class="row">
            <label>
                <span>Nro de aula</span>
                <input type="number" name="nroaula" required min="1" placeholder="Ej: 101" />
            </label>
            <label>
                <span>ID de módulo</span>
                <input type="number" name="id_modulo" required min="1" placeholder="Ej: 1" />
            </label>
        </div>

        <div class="row">
            <label>
                <span>Capacidad</span>
                <input type="number" name="capacidad" required min="1" placeholder="Ej: 30" />
            </label>
            <label>
                <span>Tipo de aula (opcional)</span>
                <input type="text" name="tipo_aula" maxlength="30" placeholder="Ej: Laboratorio" />
            </label>
        </div>

        <label style="align-items:center; grid-template-columns:auto 1fr; gap:.5rem;">
            <input type="checkbox" name="disponible" checked />
            <span>Disponible</span>
        </label>

        <div class="actions">
            <button type="submit">Registrar aula</button>
            <button class="secondary" id="btnEjemplo" type="button">Rellenar ejemplo</button>
            <button class="secondary" id="btnLimpiar" type="button">Limpiar</button>
        </div>
    </form>

    <h2>Respuesta</h2>
    <pre id="response">Esperando envío…</pre>

    <script>
        const form = document.getElementById('aulaForm');
        const out = document.getElementById('response');
        const btnEjemplo = document.getElementById('btnEjemplo');
        const btnLimpiar = document.getElementById('btnLimpiar');

        btnEjemplo.addEventListener('click', () => {
            form.nroaula.value = 101;
            form.id_modulo.value = 1; // Asegúrate que exista en tu BD
            form.capacidad.value = 30;
            form.tipo_aula.value = 'Teórica';
            form.disponible.checked = true;
        });

        btnLimpiar.addEventListener('click', () => {
            form.reset();
            out.textContent = 'Esperando envío…';
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            out.textContent = 'Enviando…';

            const formData = new FormData(form);
            const payload = {
                nroaula: Number(formData.get('nroaula')),
                id_modulo: Number(formData.get('id_modulo')),
                capacidad: Number(formData.get('capacidad')),
                tipo_aula: formData.get('tipo_aula')?.trim() || null,
                disponible: formData.get('disponible') !== null
            };

            try {
                const res = await fetch('/api/aulas/registrar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const contentType = res.headers.get('content-type') || '';
                const body = contentType.includes('application/json')
                    ? await res.json()
                    : await res.text();

                out.textContent = JSON.stringify({ status: res.status, body }, null, 2);
            } catch (err) {
                out.textContent = 'Error: ' + err.message;
            }
        });
    </script>
</body>
</html>

