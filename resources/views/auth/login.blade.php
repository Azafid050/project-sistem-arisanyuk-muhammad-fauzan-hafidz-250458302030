<x-guest-layout>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name', 'ArisanYuk') }} - Masuk</title>

<!-- Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
</style>
</head>

<body class="font-poppins text-gray-900 bg-gray-50 flex items-center justify-center min-h-screen">

  <!-- Card Login -->
  <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden grid grid-cols-1 md:grid-cols-2">

    <!-- Sisi kiri -->
    <div class="hidden md:flex flex-col justify-center p-12 bg-gradient-to-br from-indigo-700 to-purple-800 text-white relative overflow-hidden">
      <svg class="absolute inset-0 w-full h-full opacity-10" viewBox="0 0 100 100" preserveAspectRatio="none">
        <defs>
          <pattern id="dot-pattern" width="10" height="10" patternUnits="userSpaceOnUse">
            <circle cx="2" cy="2" r="1" fill="white" />
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#dot-pattern)" />
      </svg>

      <div class="relative z-10 space-y-6">
        <div>
          <h1 class="font-playfair text-4xl font-bold mb-4">Selamat Datang Kembali!</h1>
          <p class="font-light opacity-90">Masuk ke dashboard Anda untuk mengelola grup arisan dengan mudah dan transparan.</p>
        </div>

        <!-- Tombol Kembali ke Beranda (Ditambahkan wire:navigate) -->
        <a href="{{ url('/') }}"
           wire:navigate
           class="inline-flex items-center text-white font-medium bg-white/10 backdrop-blur-sm hover:bg-white/20 border border-white/30 px-4 py-2 rounded-lg transition transform hover:scale-[1.03]">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          <span>Kembali ke Beranda</span>
        </a>
      </div>
    </div>

    <!-- Sisi kanan -->
    <div class="p-8 md:p-12 text-gray-900 flex flex-col justify-center">
      <h2 class="font-playfair text-3xl font-bold mb-2">Masuk Akun</h2>
      <p class="text-gray-600 mb-6">
        Belum punya akun?
        <!-- Tautan Daftar Sekarang (Ditambahkan wire:navigate) -->
        <a href="{{ route('register') }}"
           wire:navigate
           class="text-indigo-600 font-medium hover:underline">
          Daftar sekarang
        </a>
      </p>

      <!-- Alert -->
      @if (session('status'))
      <div id="status-alert" class="mb-4 p-4 rounded-lg text-sm text-green-800 bg-green-100 border border-green-300 animate-fade-in">
        {{ session('status') }}
      </div>
      @endif

      @if ($errors->any())
      <div id="error-alert" class="mb-4 p-4 rounded-lg text-sm text-red-800 bg-red-100 border border-red-300 animate-fade-in">
        <p class="font-bold mb-2">Oops! Terjadi kesalahan:</p>
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <!-- Form -->
      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
          <label for="email" class=" block text-sm font-medium text-gray-700">Email</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="p-2 block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-600 focus:ring-indigo-600 transition" />
        </div>

        <div class="mb-4">
          <label for="password" class=" block text-sm font-medium text-gray-700">Password</label>
          <input id="password" type="password" name="password" required autocomplete="current-password"
                class="p-2 block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-600 focus:ring-indigo-600 transition" />
        </div>

        <div class="flex items-center justify-between mb-6">
          <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-600">
            <span class="ms-2 text-sm text-gray-600">Remember me</span>
          </label>
          @if (Route::has('password.request'))
          <!-- Tautan Lupa Kata Sandi (Ditambahkan wire:navigate) -->
          <a href="{{ route('password.request') }}"
             wire:navigate
             class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline transition">
            Lupa kata sandi?
          </a>
          @endif
        </div>

        <button type="submit"
                class="w-full px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-bold text-lg shadow-lg hover:from-indigo-700 hover:to-purple-700 transition transform hover:scale-[1.02]">
          Masuk
        </button>
      </form>
    </div>
  </div>

  <!-- Alert Auto-close -->
  <script>
  function setupAlertAutoClose(id) {
    setTimeout(() => {
      const el = document.getElementById(id);
      if (el) {
        el.classList.add('opacity-0', 'transition', 'duration-500');
        setTimeout(() => el.remove(), 500);
      }
    }, 3000);
  }
  setupAlertAutoClose('status-alert');
  setupAlertAutoClose('error-alert');
  </script>

</body>
</html>
</x-guest-layout>