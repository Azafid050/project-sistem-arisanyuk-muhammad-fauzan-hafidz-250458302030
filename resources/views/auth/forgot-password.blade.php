<x-guest-layout>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name', 'ArisanYuk') }} - Lupa Password</title>

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
        
        <div class="w-full max-w-lg mb-6">
            <a href="{{ route('login') }}"
                wire:navigate
                class=" text-gray-800 hover:text-indigo-700 flex items-center transition p-3 rounded-xl bg-white shadow-lg border border-transparent hover:border-indigo-400 transform hover:scale-[1.03] w-max">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span class="font-semibold">Kembali ke Masuk</span>
            </a>
        </div>

        <div class="relative max-w-lg w-full mx-auto rounded-3xl overflow-hidden shadow-2xl shadow-indigo-100/50">
            <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 opacity-40 blur-3xl animate-glow rounded-3xl"></div>

            <div class="relative z-10 bg-white border border-gray-100 rounded-3xl transform transition-all duration-500 hover:scale-[1.01]">

                <div class="p-8 pb-6 bg-gradient-to-br from-indigo-700 to-purple-800 text-white">
                    <h1 class="font-playfair text-3xl font-bold flex items-center">
                        <svg class="w-8 h-8 mr-3 text-yellow-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"></path></svg>
                        Lupa Kata Sandi?
                    </h1>
                    <p class="font-light opacity-90 mt-2">
                        Jangan khawatir. Cukup masukkan alamat email Anda di bawah, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
                    </p>
                </div>

                <div class="p-8 pt-6 text-gray-900">
                    
                    @if (session('status'))
                    <div id="status-alert" class="mb-4 p-4 rounded-lg text-sm text-green-800 bg-green-100 border border-green-300 animate-fade-in font-medium">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input id="email" 
                                class="p-1 block mt-1 w-full border-indigo-300 rounded-lg shadow-sm focus:border-indigo-600 focus:ring-indigo-600 transition" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                autocomplete="username"
                            />
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full px-10 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-bold text-lg shadow-2xl shadow-indigo-600/50 hover:from-indigo-700 hover:to-purple-700 transition transform hover:scale-[1.02]">
                            Kirim Tautan Atur Ulang
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
                alert.classList.add('opacity-0', 'transition', 'duration-500');
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000); 
    }
    setupAlertAutoClose('status-alert');
    </script>

</body>
</html>
</x-guest-layout>