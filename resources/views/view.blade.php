<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Prueba - Gestión Aulas Horarios</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        label { display:block; margin-top: .6rem; }
        input { width: 300px; padding: .4rem; }
        button { margin-top: .8rem; padding: .5rem 1rem; }
        pre { background:#f6f8fa; padding:1rem; border:1px solid #e1e4e8; }
    </style>
    </head>
<body>
    <h1>Probar endpoint: /api/aulas/registrar</h1>

    <form id="aulaForm">
        <label>Nro de aula
            <input type="number" name="nroaula" required min="1" />
        </label>

        <label>ID de módulo
            <input type="number" name="id_modulo" required min="1" />
        </label>

        <label>Capacidad
            <input type="number" name="capacidad" required min="1" />
        </label>

        <label>Tipo de aula (opcional)
            <input type="text" name="tipo_aula" maxlength="30" />
        </label>

        <label>
            <input type="checkbox" name="disponible" checked /> Disponible
        </label>

        <button type="submit">Registrar aula</button>
    </form>

    <h2>Respuesta</h2>
    <pre id="response">Esperando envío...</pre>

    <script>
        const form = document.getElementById('aulaForm');
        const out = document.getElementById('response');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            out.textContent = 'Enviando...';

            const formData = new FormData(form);
            const payload = {
                nroaula: Number(formData.get('nroaula')),
                id_modulo: Number(formData.get('id_modulo')),
                capacidad: Number(formData.get('capacidad')),
                tipo_aula: formData.get('tipo_aula') || null,
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

                const data = await res.json().catch(() => ({ status: res.status, text: 'No JSON' }));
                out.textContent = JSON.stringify({ status: res.status, body: data }, null, 2);
            } catch (err) {
                out.textContent = 'Error: ' + err.message;
            }
        });
    </script>
</body>
</html>

