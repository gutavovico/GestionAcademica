<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso de Usuarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="min-h-screen bg-[#eaf3ff] flex items-center justify-center p-6">
  <div class="w-full max-w-lg">
    <div class="flex flex-col items-center mb-6">
      <div class="h-16 w-16 rounded-full bg-cyan-500 flex items-center justify-center shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="h-8 w-8">
          <path d="M12 3c-2.21 0-4 1.79-4 4v1H5v5c0 3.31 2.69 6 6 6s6-2.69 6-6V8h-3V7c0-2.21-1.79-4-4-4z"/>
        </svg>
      </div>
      <h1 class="mt-3 text-xl font-semibold text-gray-800">Acceso de Usuarios</h1>
      <p class="text-gray-500">Accede a tu cuenta</p>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-6">
      <h2 class="text-lg font-semibold text-gray-900">Inicio de Sesion</h2>
      <p class="text-gray-500 mb-4">Ingresa tus credenciales para continuar</p>

      @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded p-3 mb-3 text-sm">{{ session('success') }}</div>
      @endif
      @if (session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 rounded p-3 mb-3 text-sm">{{ session('info') }}</div>
      @endif
      @if (session('status'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 rounded p-3 mb-3 text-sm">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded p-3 mb-3 text-sm">
          {{ $errors->first() }}
        </div>
      @endif

      <form id="loginForm" method="POST" action="/login" novalidate>
        @csrf
        <div class="space-y-4">
          <div>
            <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
            <div class="flex items-center gap-2 bg-gray-100 rounded-lg px-3">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-gray-400"><path stroke-width="2" d="M4 4h16v16H4z"/><path stroke-width="2" d="M22 6l-10 7L2 6"/></svg>
              <input type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="correo@dominio.com" required autofocus
                     class="w-full bg-transparent py-2 outline-none placeholder:text-gray-400 @error('correo') border-red-500 @enderror">
            </div>
            @error('correo')
              <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <div class="flex items-center gap-2 bg-gray-100 rounded-lg px-3">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-gray-400"><path stroke-width="2" d="M12 11V7a4 4 0 118 0v4"/><rect x="6" y="11" width="12" height="9" rx="2" stroke-width="2"/></svg>
              <input type="password" id="contrasena" name="contrasena" placeholder="*****" required
                     class="w-full bg-transparent py-2 outline-none placeholder:text-gray-400 @error('contrasena') border-red-500 @enderror">
              <button type="button" id="togglePass" class="text-sm text-indigo-600 hover:text-indigo-800">Mostrar</button>
            </div>
            @error('contrasena')
              <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div class="text-right -mt-1">
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-sky-600 hover:text-sky-700">Olvidaste tu contraseña?</a>
          </div>

          <button id="submitBtn" type="submit"
                  class="w-full bg-neutral-900 hover:bg-black text-white rounded-lg py-2.5 transition disabled:opacity-60 disabled:cursor-not-allowed">
            <span id="btnText">Ingresar</span>
            <span id="btnSpinner" class="hidden ml-2 inline-block h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin align-middle"></span>
          </button>

          <!-- Registro opcional -->
          <!-- <div class="text-center text-sm text-gray-600">No tienes cuenta? <a href="/register" class="text-indigo-600 hover:text-indigo-800 underline">Reg�strate</a></div> -->
        </div>
      </form>
    </div>
  </div>

  <script>
    (function () {
      const form = document.getElementById('loginForm');
      const submitBtn = document.getElementById('submitBtn');
      const btnText = document.getElementById('btnText');
      const btnSpinner = document.getElementById('btnSpinner');
      const correo = document.getElementById('correo');
      const pass = document.getElementById('contrasena');
      const toggle = document.getElementById('togglePass');

      if (toggle && pass) {
        toggle.addEventListener('click', function () {
          const showing = pass.getAttribute('type') === 'text';
          pass.setAttribute('type', showing ? 'password' : 'text');
          this.textContent = showing ? 'Mostrar' : 'Ocultar';
        });
      }

      if (form) {
        form.addEventListener('submit', function (e) {
          const emailOk = correo.value && /\S+@\S+\.\S+/.test(correo.value);
          const passOk = pass.value && pass.value.length > 0;
          if (!emailOk || !passOk) {
            e.preventDefault();
            alert('Verifica tu correo y contraseña.');
            return false;
          }
          submitBtn.disabled = true;
          btnText.textContent = 'Ingresando...';
          btnSpinner.classList.remove('hidden');
        });
      }
    })();
  </script>
</body>
</html>
