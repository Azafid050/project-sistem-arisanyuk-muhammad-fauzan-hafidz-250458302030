<x-guest-layout>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name', 'ArisanYuk') }} - Daftar Akun</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes glow {
    0% { transform: rotate(0deg) scale(1); opacity: 0.3; }
    50% { transform: rotate(180deg) scale(1.03); opacity: 0.6; }
    100% { transform: rotate(360deg) scale(1); opacity: 0.3; }
}
.animate-glow {
    animation: glow 6s ease-in-out infinite;
}
</style>
</head>

<body class="font-poppins text-gray-900 bg-gray-50 flex items-center justify-center min-h-screen">

    <main class="w-full flex flex-col items-center justify-center relative">

        <div class="w-full max-w-lg mb-6"> <a href="{{ url('/') }}"
                wire:navigate
                class=" text-gray-800 hover:text-indigo-700 flex items-center transition p-3 rounded-xl bg-white shadow-lg border border-transparent hover:border-indigo-400 transform hover:scale-[1.03] w-max">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span class="font-semibold">Kembali ke Beranda</span>
            </a>
        </div>

        <div class="relative max-w-lg w-full mx-auto rounded-3xl overflow-hidden shadow-2xl shadow-indigo-100/50">
            <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 opacity-40 blur-3xl animate-glow rounded-3xl"></div>

            <div class="relative z-10 bg-white border border-gray-100 rounded-3xl transform transition-all duration-500 hover:scale-[1.01]">

                <div class="p-8 pb-6 bg-gradient-to-br from-indigo-700 to-purple-800 text-white">
                    <h1 class="font-playfair text-3xl font-bold">Selamat Bergabung!</h1>
                    <p class="font-light opacity-90 mt-1">Satu langkah lagi untuk memulai era baru arisan yang transparan.</p>
                </div>

                <div class="p-8 pt-6 text-gray-900">
                    <h2 class="font-playfair text-2xl font-bold mb-2">Daftar Akun Baru</h2>
                    <p class="text-gray-600 mb-6">
                        Lengkapi detail Anda. Sudah punya akun?
                        <a href="{{ route('login') }}"
                            wire:navigate
                            class="text-indigo-600 font-medium hover:underline">
                            Masuk di sini
                        </a>
                    </p>

                    @if (session('status'))
                    <div id="status-alert" class="mb-4 p-4 rounded-lg text-sm text-green-800 bg-green-100 border border-green-300 animate-fade-in">
                        {{ session('status') }}
                    </div>
                    @endif

                    @if ($errors->any())
                    <div id="error-alert" class="mb-4 p-4 rounded-lg text-sm text-red-800 bg-red-100 border border-red-300 animate-fade-in">
                        <p class="font-bold mb-2">Oops! Ada yang perlu diperbaiki:</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input id="name" class="p-1 block mt-1 w-full border-indigo-300 rounded-lg shadow-sm focus:border-indigo-600 focus:ring-indigo-600 transition"
                                    type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" class="p-1 block mt-1 w-full border-indigo-300 rounded-lg shadow-sm focus:border-indigo-600 focus:ring-indigo-600 transition"
                                    type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input id="password" class="p-1 block mt-1 w-full border-indigo-300 rounded-lg shadow-sm focus:border-indigo-600 focus:ring-indigo-600 transition"
                                    type="password" name="password" required autocomplete="new-password" />
                        </div>

                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                            <input id="password_confirmation" class="p-1 block mt-1 w-full border-indigo-300 rounded-lg shadow-sm focus:border-indigo-600 focus:ring-indigo-600 transition"
                                    type="password" name="password_confirmation" required autocomplete="new-password" />
                        </div>
                        
                        <div class="mb-8 p-4 border border-indigo-200 rounded-xl bg-indigo-50">
                            <label class="block text-sm font-semibold text-indigo-700 mb-3">Daftar Sebagai:</label>
                            
                            <div class="flex flex-wrap gap-6">
                                
                                <label for="role_anggota" class="flex items-center cursor-pointer p-3 bg-white rounded-lg shadow-md hover:shadow-lg transition">
                                    <input type="radio" 
                                           id="role_anggota"
                                           name="role" 
                                           value="anggota" 
                                           required 
                                           class="form-radio text-indigo-600 focus:ring-indigo-500 h-5 w-5 border-gray-300" 
                                           {{ old('role', 'anggota') == 'anggota' ? 'checked' : '' }}>
                                    <span class="ml-3 text-gray-800 font-medium">Anggota</span>
                                    <span class="ml-2 text-sm text-gray-500 hidden sm:inline">(Bergabung ke arisan)</span>
                                </label>
                                
                                <label for="role_bendahara" class="flex items-center cursor-pointer p-3 bg-white rounded-lg shadow-md hover:shadow-lg transition">
                                    <input type="radio" 
                                           id="role_bendahara"
                                           name="role" 
                                           value="bendahara" 
                                           required 
                                           class="form-radio text-indigo-600 focus:ring-indigo-500 h-5 w-5 border-gray-300" 
                                           {{ old('role') == 'bendahara' ? 'checked' : '' }}>
                                    <span class="ml-3 text-gray-800 font-medium">Bendahara</span>
                                    <span class="ml-2 text-sm text-gray-500 hidden sm:inline">(Membuat dan mengelola arisan)</span>
                                </label>
                            </div>
                            
                            @error('role')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full px-10 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-bold text-lg shadow-2xl shadow-indigo-600/50 hover:from-indigo-700 hover:to-purple-700 transition transform hover:scale-[1.02]">
                            Buat Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

<script>
function setupAlertAutoClose(alertId) {
    setTimeout(() => {
        const alert = document.getElementById(alertId);
        if (alert) {
            // Animasi fade out
            alert.classList.add('opacity-0', 'transition', 'duration-500');
            // Hapus elemen setelah animasi selesai
            setTimeout(() => alert.remove(), 500);
        }
    }, 5000); // Durasi tampil 5 detik
}
setupAlertAutoClose('status-alert');
setupAlertAutoClose('error-alert');
</script>

</body>
</html>
</x-guest-layout>