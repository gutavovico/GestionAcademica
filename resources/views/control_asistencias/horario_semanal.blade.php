<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Horario Semanal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style></style>
</head>
<body class="min-h-screen bg-[#eef5ff]">
  <header class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Horario semanal</h1>
      <p class="text-slate-600">{{ $user?->nombre ?? 'Docente' }}</p>
    </div>
    <a href="/dashboard" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-800 shadow-sm">← Volver al perfil</a>
  </header>

  <main class="max-w-6xl mx-auto px-6 pb-16 space-y-6">
    <section class="bg-white rounded-2xl shadow border border-slate-200">
      <div class="p-6 border-b border-slate-200 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <h2 class="font-semibold text-slate-900">Parrilla semanal</h2>
      </div>

      <div id="contenedor" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6"></div>
      <div id="vacio" class="p-6 hidden text-slate-600">No tienes horarios asignados esta semana.</div>
    </section>
  </main>

  <script>
    const cont = document.getElementById('contenedor');
    const vacio = document.getElementById('vacio');

    function fmt(t){ return (t||'').toString().slice(0,5); }

    function //">${asis}</span></div>`;
          const right = document.createElement('div');
          right.className='text-right';
          right.innerHTML = `<div class="text-slate-800">${fmt(it.hora_ini)} - ${fmt(it.hora_fin)}</div>
                             <div class="text-slate-600 text-sm">${it.aula || ''}${it.modulo ? ' - Mod. '+it.modulo : ''}</div>`;
          row.appendChild(left); row.appendChild(right); body.appendChild(row);
        });
      }
      wrap.appendChild(head); wrap.appendChild(body); return wrap;
    }

    async function cargar(){
      try {
        const res = await fetch('/docente/mi-horario-semanal', { headers:{ 'Accept':'application/json' } });
        if (!res.ok){ cont.innerHTML=''; vacio.classList.remove('hidden'); return; }
        const data = await res.json();
        cont.innerHTML = '';
        const orden = ['Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo'];
        const diasData = (data && data.dias) ? data.dias : {};
        const norm = s => (s||'').normalize ? (s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase() : (s||'').toLowerCase();
        const keyFor = d => Object.keys(diasData).find(k => norm(k) === norm(d)) || d;
        let total = 0;
        orden.forEach(d => {
          const k = keyFor(d);
          const items = diasData[k] || [];
          total += items.length;
          cont.appendChild(cardDia(d, items));
        });
        vacio.classList.toggle('hidden', total > 0);
      } catch(err){
        cont.innerHTML=''; vacio.textContent = 'Error al cargar los datos'; vacio.classList.remove('hidden');
      }
    }

    cargar();
  </script>
</body>
</html>
