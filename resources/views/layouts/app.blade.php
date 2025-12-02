<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArisanYuk</title>
    

    @vite('resources/css/app.css')
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-[#FAF8F5] text-gray-800 flex min-h-screen">

    @php
        $role = Auth::user()->role ?? 'guest';
    @endphp

    @if ($role === 'admin')
        @include('partials.sidebars.admin')
    @elseif ($role === 'bendahara')
        @include('partials.sidebars.bendahara')
    @elseif ($role === 'anggota')
        @include('partials.sidebars.user')
    @endif

    

    <div class="flex-1 flex flex-col">
        @include('partials.navbar') 

        <main class="flex-1 p-6">
            {{ $slot }}
        </main>

        @include('partials.footer')
    </div>

    @livewireScripts
    
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const hamburger = document.getElementById('hamburger');
    // DIGANTI: Mengganti 'sidebar' menjadi 'mobileSidebarElement' untuk menghindari konflik Livewire
    const mobileSidebarElement = document.getElementById('mobileSidebar'); 
    const overlay = document.getElementById('mobileSidebarOverlay');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const userMenuButton = document.getElementById('userMenuButton');
    const dropdownMenu = document.getElementById('userDropdown');
    const userDropdownWrapper = document.getElementById('userDropdownWrapper');

    // Element Logout
    const logoutLink = document.getElementById('logoutLink');
    const logoutForm = document.getElementById('logoutForm');

    const exists = el => el !== null && el !== undefined;

    // --- LOGIC SIDEBAR ---

    function openSidebar() {
        if (!exists(mobileSidebarElement) || !exists(overlay)) return;
        mobileSidebarElement.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('opacity-100'), 10);
        // PERBAIKAN: Set aria-hidden ke false saat terbuka
        mobileSidebarElement.setAttribute('aria-hidden', 'false'); 
    }

    function closeSidebar() {
        if (!exists(mobileSidebarElement) || !exists(overlay)) return;
        mobileSidebarElement.classList.add('-translate-x-full');
        overlay.classList.remove('opacity-100');
        setTimeout(() => overlay.classList.add('hidden'), 300);
        // PERBAIKAN: Set aria-hidden ke true saat tertutup
        mobileSidebarElement.setAttribute('aria-hidden', 'true'); 
    }

    if (exists(hamburger)) {
        // Klik Hamburger membuka Sidebar
        hamburger.addEventListener('click', e => {
            e.stopPropagation(); 
            openSidebar();
        });
    }

    if (exists(closeSidebarBtn)) {
        closeSidebarBtn.addEventListener('click', closeSidebar);
    }

    if (exists(overlay)) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Penutup sidebar saat link diklik (khusus mobile)
    if (exists(mobileSidebarElement)) {
        mobileSidebarElement.querySelectorAll('a').forEach(a =>
            a.addEventListener('click', () => {
                // Cek jika ukuran mobile (di bawah 768px)
                if (window.innerWidth < 768) {
                    setTimeout(() => closeSidebar(), 120); 
                }
            })
        );
    }

    // --- LOGIC DROPDOWN & LOGOUT ---

    function toggleDropdown(shouldClose) { // true = tutup, false = buka
        if (!exists(dropdownMenu)) return;
        if (shouldClose) { // TUTUP
            dropdownMenu.classList.add('opacity-0', 'invisible', 'scale-95', 'pointer-events-none');
            dropdownMenu.classList.remove('opacity-100', 'scale-100');
        } else { // BUKA
            dropdownMenu.classList.remove('opacity-0', 'invisible', 'scale-95', 'pointer-events-none');
            dropdownMenu.classList.add('opacity-100', 'scale-100');
        }
    }

    // Toggle dropdown user
    if (exists(userMenuButton)) {
        userMenuButton.addEventListener('click', e => {
            e.stopPropagation(); 
            const isVisible = dropdownMenu.classList.contains('opacity-100');
            toggleDropdown(isVisible); 
        });
    }

    // Menghentikan propagasi klik di dalam wrapper dropdown
    if (exists(userDropdownWrapper)) {
        userDropdownWrapper.addEventListener('click', e => {
            e.stopPropagation(); 
        });
    }

    // Klik luar tutup dropdown
    document.addEventListener('click', e => {
        const isDropdownVisible = exists(dropdownMenu) && dropdownMenu.classList.contains('opacity-100');

        if (isDropdownVisible && exists(userDropdownWrapper) && !userDropdownWrapper.contains(e.target)) {
            toggleDropdown(true); // Tutup
        }
    });

    // Logout Handler (Mengirim form tersembunyi)
    if (exists(logoutLink) && exists(logoutForm)) {
        logoutLink.addEventListener('click', (e) => {
            e.preventDefault();
            toggleDropdown(true); 
            logoutForm.submit();
        });
    }
});
</script>
</body>
</html>