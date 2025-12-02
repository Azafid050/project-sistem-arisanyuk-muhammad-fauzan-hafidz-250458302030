<x-guest-layout>
<div class="min-h-screen flex flex-col bg-gray-50 font-poppins antialiased relative overflow-hidden">

<!-- Efek Gradien Latar Belakang Halus -->
<div class="absolute inset-0 z-0 bg-gradient-to-br from-indigo-50 to-white opacity-10"></div>

<!-- Konten Utama (Hero Section) - Estetika & Carousel -->
<!-- KOREKSI KRUSIAL: z-10 dikembalikan untuk memastikan konten utama (tombol Gabung/Masuk) berada di atas background overlay (z-0) -->
<main class="relative z-10 flex-grow flex items-center justify-center p-4">

    <div class="max-w-7xl w-full grid grid-cols-1 lg:grid-cols-2 gap-16 items-center bg-white rounded-3xl shadow-2xl shadow-indigo-100/50 border border-indigo-100 p-8 md:p-12">

        <!-- Sisi Kiri: Teks dan Aksi -->
        <div class="text-center lg:text-left">

            <h1 class="font-playfair text-6xl sm:text-7xl font-bold text-gray-900 mb-6 leading-tight">
                Arisan Modern, <span class="text-indigo-600">Transparan</span>, Penuh Gaya.
            </h1>

            <p class="text-gray-600 mb-10 text-xl max-w-lg mx-auto lg:mx-0 font-light">
                Kelola arisan Anda dengan platform digital yang elegan, aman, dan mudah diakses kapan saja, di mana saja.
            </p>

            <!-- Tombol Aksi Utama -->
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center lg:justify-start mb-12">
                {{-- Tambahkan wire:navigate --}}
                <a 
                    href="{{ route('register') }}" 
                    wire:navigate
                    class="px-10 py-4 bg-indigo-600 text-white rounded-full font-semibold text-lg shadow-xl shadow-indigo-500/40 hover:bg-indigo-700 transition transform hover:scale-105"
                >
                    Gabung Sekarang
                </a>
                {{-- Tambahkan wire:navigate --}}
                <a 
                    href="{{ route('login') }}" 
                    wire:navigate
                    class="px-10 py-4 border border-gray-300 bg-white text-gray-800 rounded-full font-semibold text-lg hover:bg-gray-100 transition transform hover:scale-105"
                >
                    Masuk Akun
                </a>
            </div>

            <!-- Statistik Kepercayaan -->
            <div class="flex justify-center lg:justify-start space-x-8 text-sm text-gray-700">
                <div class="text-center lg:text-left">
                    <p class="text-3xl font-bold text-gray-900">15K+</p>
                    <span>Pengguna Aktif</span>
                </div>
                <div class="text-center lg:text-left">
                    <p class="text-3xl font-bold text-gray-900">100%</p>
                    <span>Aman & Terpercaya</span>
                </div>
                <div class="text-center lg:text-left">
                    <p class="text-3xl font-bold text-gray-900">24/7</p>
                    <span>Dukungan Penuh</span>
                </div>
            </div>


        </div>

        <!-- Sisi Kanan: Carousel Gambar Arisan (Struktur HTML/CSS) -->

        <div class="flex justify-center p-4 relative">
            <div class="w-full max-w-lg aspect-video bg-gray-100 rounded-2xl shadow-2xl overflow-hidden border-4 border-indigo-200">
                <!-- Konten Carousel -->
                <div id="arisanCarousel" class="flex transition-transform duration-500 ease-in-out">
                    <!-- Slide 1 -->
                    <div class="w-full flex-shrink-0 flex items-center justify-center p-8 bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-center">
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold font-playfair">Mulai Arisanmu Sendiri</h3>
                            <p class="text-sm opacity-90">Buat grup, undang teman, atur jadwal mudah.</p>
                            <!-- KOREKSI URL GAMBAR -->
                            <img src="https://picsum.photos/400/250?random=1" alt="Arisan Grup" class="mt-4 mx-auto rounded-lg shadow-lg">
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="w-full flex-shrink-0 flex items-center justify-center p-8 bg-gradient-to-br from-green-500 to-blue-600 text-white text-center">
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold font-playfair">Lacak Pembayaran Otomatis</h3>
                            <p class="text-sm opacity-90">Notifikasi tepat waktu, laporan transparan.</p>
                            <!-- KOREKSI URL GAMBAR -->
                            <img src="https://picsum.photos/400/250?random=2" alt="Lacak Pembayaran" class="mt-4 mx-auto rounded-lg shadow-lg">
                        </div>
                    </div>
                    <!-- Slide 3 -->
                    <div class="w-full flex-shrink-0 flex items-center justify-center p-8 bg-gradient-to-br from-orange-500 to-red-600 text-white text-center">
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold font-playfair">Menangkan Arisanmu</h3>
                            <p class="text-sm opacity-90">Pengundian adil, dana langsung cair.</p>
                            <!-- KOREKSI URL GAMBAR -->
                            <img src="https://picsum.photos/400/250?random=3" alt="Pemenang Arisan" class="mt-4 mx-auto rounded-lg shadow-lg">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indikator / Navigasi Carousel -->
            <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-2">
                <button class="w-3 h-3 bg-indigo-600 rounded-full focus:outline-none"></button>
                <button class="w-3 h-3 bg-gray-300 rounded-full focus:outline-none"></button>
                <button class="w-3 h-3 bg-gray-300 rounded-full focus:outline-none"></button>
            </div>


        </div>

    </div>

</main>

<!-- BAGIAN BARU 1: CARA KERJA (How It Works) -->

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-playfair text-4xl sm:text-5xl font-bold text-gray-900 mb-4">Mulai Hanya Dalam 3 Langkah</h2>
        <p class="text-gray-600 text-xl mb-16 max-w-3xl mx-auto">
            ArisanYuk dirancang untuk kemudahan maksimal. Kelola grup Anda, lacak pembayaran, dan undi pemenang secara transparan.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

            <!-- Langkah 1: Buat Grup -->
            <div class="p-8 rounded-xl shadow-xl border-t-4 border-indigo-500 bg-gray-50 hover:shadow-2xl transition duration-300">
                <div class="w-16 h-16 mx-auto mb-6 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full text-2xl font-extrabold">1</div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-3">Buat Grup & Atur Jadwal</h3>
                <p class="text-gray-600">Tentukan nama arisan, jumlah peserta, besaran iuran, dan periode waktu. Semua diatur di dashboard.</p>
            </div>

            <!-- Langkah 2: Lacak Pembayaran -->
            <div class="p-8 rounded-xl shadow-xl border-t-4 border-indigo-500 bg-gray-50 hover:shadow-2xl transition duration-300">
                <div class="w-16 h-16 mx-auto mb-6 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full text-2xl font-extrabold">2</div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-3">Notifikasi & Pembayaran</h3>
                <p class="text-gray-600">Anggota mendapatkan notifikasi otomatis saat jatuh tempo. Lacak siapa yang sudah bayar secara *real-time*.</p>
            </div>

            <!-- Langkah 3: Undi Pemenang -->
            <div class="p-8 rounded-xl shadow-xl border-t-4 border-indigo-500 bg-gray-50 hover:shadow-2xl transition duration-300">
                <div class="w-16 h-16 mx-auto mb-6 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full text-2xl font-extrabold">3</div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-3">Undian Transparan & Adil</h3>
                <p class="text-gray-600">Sistem melakukan pengundian acak yang transparan dan mencatat pemenang untuk putaran berikutnya.</p>
            </div>


        </div>

    </div>

</section>

<!-- BAGIAN BARU 2: TESTIMONI KEPERCAYAAN (Social Proof) -->

<!-- z-20 dipertahankan agar tombol CTA di bagian ini juga tetap di lapisan terdepan -->
<section class="relative z-20 py-20 bg-indigo-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-playfair text-4xl sm:text-5xl font-bold text-gray-900 mb-12">Kata Mereka yang Sudah Percaya</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- Testimoni 1 -->
            <div class="p-8 bg-white rounded-xl shadow-2xl border-b-4 border-green-500 transform hover:-translate-y-1 transition duration-300">
                <p class="text-lg italic text-gray-700 mb-6">
                    "Sejak pakai ArisanYuk, nggak ada lagi drama lupa bayar atau bingung siapa yang sudah menang. Transparansinya 100%! Sangat direkomendasikan."
                </p>
                <div class="flex items-center justify-center">
                    <img class="w-12 h-12 rounded-full object-cover mr-4" src="https://placehold.co/100x100/A5B4FC/374151?text=A" alt="Foto Profil">
                    <div class="text-left">
                        <p class="font-semibold text-gray-900">Andi Pratama</p>
                        <p class="text-sm text-gray-500">Ketua Arisan Keluarga</p>
                    </div>
                </div>
            </div>

            <!-- Testimoni 2 -->
            <div class="p-8 bg-white rounded-xl shadow-2xl border-b-4 border-yellow-500 transform hover:-translate-y-1 transition duration-300">
                <p class="text-lg italic text-gray-700 mb-6">
                    "Desainnya estetik dan gampang banget dipakai, bahkan oleh yang kurang melek teknologi. Fitur notifikasinya sangat membantu."
                </p>
                <div class="flex items-center justify-center">
                    <img class="w-12 h-12 rounded-full object-cover mr-4" src="https://placehold.co/100x100/FDBA74/374151?text=B" alt="Foto Profil">
                    <div class="text-left">
                        <p class="font-semibold text-gray-900">Bunga Citra</p>
                        <p class="text-sm text-gray-500">Anggota Arisan Kantor</p>
                    </div>
                </div>
            </div>

            <!-- Testimoni 3 -->
            <div class="p-8 bg-white rounded-xl shadow-2xl border-b-4 border-indigo-500 transform hover:-translate-y-1 transition duration-300">
                <p class="text-lg italic text-gray-700 mb-6">
                    "Keamanan dana terjamin, dan pengundiannya fair karena terekam sistem. Ini benar-benar membuat arisan lebih modern dan bebas konflik."
                </p>
                <div class="flex items-center justify-center">
                    <img class="w-12 h-12 rounded-full object-cover mr-4" src="https://placehold.co/100x100/D1D5DB/374151?text=C" alt="Foto Profil">
                    <div class="text-left">
                        <p class="font-semibold text-gray-900">Cahyo Utomo</p>
                        <p class="text-sm text-gray-500">Pengelola Grup Investasi</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- CTA Sederhana di bawah Testimoni -->

        <div class="mt-16">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4 my-4 py-4">Siap untuk Arisan Tanpa Ribet?</h3>
            <a
                href="{{ route('register') }}"
                wire:navigate
                class="px-12 py-4 bg-indigo-600 text-white rounded-full font-bold text-lg shadow-xl shadow-indigo-600/40 hover:bg-indigo-700 transition transform hover:scale-105"
            >
                Daftar & Rasakan Bedanya
            </a>
        </div>

    </div>

</section>


</div>
</x-guest-layout>