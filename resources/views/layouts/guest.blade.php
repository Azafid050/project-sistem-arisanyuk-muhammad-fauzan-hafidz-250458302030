<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ArisanYuk</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<!-- Mengubah background body menjadi gray-50 polos agar sesuai dengan base warna di guest_page_blade.html -->
<body class="font-sans text-gray-900 min-h-screen bg-gray-50 flex flex-col">

<!-- Header / Navbar -->

<!-- [PERBARUI] Mengubah header menjadi semi-transparan dengan efek blur (Frosted Glass) -->

<header class="w-full py-5 bg-white/95 backdrop-blur-sm shadow-xl shadow-indigo-200/50 sticky top-0 z-20">
<div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
    <a href="/" class="text-3xl font-extrabold text-indigo-600 tracking-wider">ArisanYuk</a>
    <div class="space-x-4">
        </div>
</div>
</header>

<!-- Main Content -->

<main class="flex-grow flex items-center justify-center">
{{ $slot }}
</main>

<!-- Footer -->

<!-- Mengubah footer menjadi putih dengan border atas halus dan text indigo-600 -->

<footer class="w-full py-6 text-center text-gray-600 text-sm bg-white border-t border-gray-100">
&copy; {{ date('Y') }} <span class="font-bold text-indigo-600">{{ config('app.name', 'ArisanYuk') }}</span>. All rights reserved.
</footer>

</body>
</html>