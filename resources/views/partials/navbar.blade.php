@php
// Mengambil peran pengguna untuk menentukan tampilan sidebar
$role = Auth::user()->role ?? 'guest';

// ⭐️ BARU: Mengambil URL foto profil yang benar dari Accessor di Model User
$profilePhotoUrl = Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=User&background=4F46E5&color=fff';
@endphp

<header class="bg-white shadow-xl shadow-indigo-100/50 flex justify-between items-center px-4 md:px-6 py-3 border-b border-indigo-100 sticky top-0 z-[50]">
    <div class="flex items-center space-x-3">
        <button id="hamburger" aria-label="Buka menu"
        class="md:hidden p-1.5 text-indigo-700 text-2xl focus:outline-none rounded-lg hover:bg-indigo-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
            d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <h1 class="text-xl font-extrabold text-indigo-700 tracking-wider md:block hidden">
            Dashboard {{ ucfirst($role) }}
        </h1>
        <h1 class="text-xl font-extrabold text-indigo-700 tracking-wider md:hidden block">
            Arisan<span class="text-purple-600 font-light">Yuk</span>
        </h1>
    </div>

    <div class="relative z-[99999]" id="userDropdownWrapper">
        <button id="userMenuButton"
            class="flex items-center space-x-2 md:space-x-3 p-1.5 md:p-2 rounded-full hover:bg-indigo-50 transition focus:outline-none">

            <div class="text-right hidden sm:block">
                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-xs text-indigo-500 capitalize">{{ Auth::user()->role ?? 'Anggota' }}</p>
            </div>

            {{-- ⭐️ PERUBAHAN DI SINI: Menggunakan $profilePhotoUrl --}}
            <img src="{{ $profilePhotoUrl }}"
                class="w-9 h-9 md:w-10 md:h-10 rounded-full border-2 border-indigo-500 object-cover" alt="User Avatar">
        </button>

        <div id="userDropdown"
            class="absolute right-0 mt-2 w-44 bg-white border border-indigo-100 rounded-lg shadow-xl opacity-0 invisible scale-95 transform transition-all duration-200 origin-top-right z-[99999] pointer-events-none">
            
            <a href="{{ route('profile.show') }}"
                wire:navigate
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 rounded-t-lg">Profil</a>
            
            <a href="#" id="logoutLink"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 rounded-b-lg cursor-pointer">
                Logout
            </a>
        </div>
        
        <form id="logoutForm" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>
    </div>
</header>

<div id="mobileSidebarOverlay"
class="fixed inset-0 bg-gray-900 bg-opacity-50 z-[55] md:hidden hidden transition-opacity duration-300 opacity-0">
</div>

<aside id="mobileSidebar"
class="fixed inset-y-0 left-0 w-64 bg-white shadow-2xl transform -translate-x-full duration-300 z-[60] md:hidden"
aria-hidden="true">
    <div class="p-6 text-2xl font-extrabold text-indigo-700 border-b-2 border-indigo-50 flex justify-between items-center">
        <span>Arisan<span class="text-purple-600 font-light">Yuk</span></span>
        <button id="closeSidebarBtn" aria-label="Tutup menu"
        class="text-gray-400 hover:text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
            d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav id="mobileSidebarNav" class="mt-4 space-y-2 px-3">
        @if ($role === 'admin')
            @include('partials.sidebars.mobile.admin')
        @elseif ($role === 'bendahara')
            @include('partials.sidebars.mobile.bendahara')
        @elseif ($role === 'anggota')
            @include('partials.sidebars.mobile.user')
        @else
            <p class="p-4 text-sm text-gray-500">Silakan login untuk melihat menu.</p>
        @endif
    </nav>
</aside>