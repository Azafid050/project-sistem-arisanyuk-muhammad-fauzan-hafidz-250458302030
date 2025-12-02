{{-- resources/views/partials/sidebars/mobile/admin.blade.php --}}

{{-- Dashboard Admin --}}
@php($isActive = request()->routeIs('admin.dashboard'))
<a href="{{ route('admin.dashboard') }}"
    wire:navigate
    class="flex items-center space-x-3 p-3 rounded-xl {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
    
    <!-- Icon: Home -->
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22"/>
    </svg>
    <span>Dashboard</span>
</a>

{{-- Kelola User --}}
@php($isActive = request()->routeIs('users.*'))
<a href="{{ route('users.index') }}"
    wire:navigate
    class="flex items-center space-x-3 p-3 rounded-xl {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
    
    <!-- Icon: User (Kelola User) -->
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
    </svg>
    <span>Kelola User</span>
</a>

{{-- Kelola Grup --}}
@php($isActive = request()->routeIs('groups.*'))
<a href="{{ route('groups.index') }}"
    wire:navigate
    class="flex items-center space-x-3 p-3 rounded-xl {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
    
    <!-- Icon: Users (Kelola Grup) -->
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
    <span>Kelola Grup</span>
</a>

{{-- Ronde Arisan --}}
@php($isActive = request()->routeIs('rounds.*'))
<a href="{{ route('rounds.admin.admin_round_index') }}"
    wire:navigate
    class="flex items-center space-x-3 p-3 rounded-xl {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
    
    <!-- Icon: Gamepad (Ronde Arisan/Pengundian) -->
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
        <path d="M6 10v2"/>
        <path d="M18 10v2"/>
        <path d="M12 20v2"/>
        <path d="M2 12h2"/>
        <path d="M20 12h2"/>
        <path d="M12 4v2"/>
        <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/>
    </svg>
    <span>Ronde Arisan</span>
</a>

{{-- Data Pembayaran --}}
@php($isActive = request()->routeIs('payments.*'))
<a href="{{ route('payments.admin.index_payment_admin') }}"
    wire:navigate
    class="flex items-center space-x-3 p-3 rounded-xl {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
    
    <!-- Icon: DollarSign (Data Pembayaran) -->
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
        <line x1="12" x2="12" y1="2" y2="22"/>
        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
    </svg>
    <span>Data Pembayaran</span>
</a>

{{-- Notifikasi --}}
@php($isActive = request()->routeIs('notification-list'))
<a href="{{ route('notification-list') }}"
    wire:navigate
    class="flex items-center space-x-3 p-3 rounded-xl {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
    
    <!-- Icon: Bell (Notifikasi) -->
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
    </svg>
    <span>Notifikasi</span>
</a>

{{-- Profil --}}
@php($isActive = request()->routeIs('profile.show'))
<a href="{{ route('profile.show') }}"
    wire:navigate
    class="flex items-center space-x-3 p-3 rounded-xl {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
    
    <!-- Icon: Settings (Profil) -->
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 0-.75 2.07l.15.54a2 2 0 0 1-1.13 1.81L3.9 12.32a2 2 0 0 0 0 1.36l.32 1.25a2 2 0 0 1 1.13 1.81l-.15.54a2 2 0 0 0 .75 2.07l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 0 .75-2.07l-.15-.54a2 2 0 0 1 1.13-1.81l1.25-.32a2 2 0 0 0 0-1.36l-.32-1.25a2 2 0 0 1-1.13-1.81l.15-.54a2 2 0 0 0-.75-2.07l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
        <circle cx="12" cy="12" r="3"/>
    </svg>
    <span>Profil</span>
</a>