<aside class="hidden md:flex w-64 bg-white shadow-2xl shadow-indigo-200/50 flex-col justify-between h-screen sticky top-0">

    <div>
        <div class="p-6 text-2xl font-extrabold text-indigo-700 border-b-2 border-indigo-50 tracking-wider">
            <span class="font-bold">Arisan</span><span class="text-purple-600 font-light">Yuk</span>
        </div>

        <nav class="mt-4 space-y-2 px-3">
            {{-- Dashboard Bendahara --}}
            @php($isActive = request()->routeIs('bendahara.dashboard'))
            <a href="{{ route('bendahara.dashboard') }}"
               wire:navigate
               class="flex items-center space-x-3 p-3 rounded-xl transition duration-150
               {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
                
                <!-- Icon: Home -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                <span>Dashboard</span>
            </a>

            {{-- Kelola Grup --}}
            @php($isActive = request()->routeIs('groups.*'))
            <a href="{{ route('groups.bendahara.index') }}"
               wire:navigate
               class="flex items-center space-x-3 p-3 rounded-xl transition duration-150
               {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
                
                <!-- Icon: Users -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Kelola Grup</span>
            </a>

            {{-- Verifikasi Pembayaran --}}
            @php($isActive = request()->routeIs('payments.verify'))
            <a href="{{ route('payments.bendahara.index_payment_bendahara') }}"
               wire:navigate
               class="flex items-center space-x-3 p-3 rounded-xl transition duration-150
               {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
                
                <!-- Icon: CheckCircle (Verifikasi) -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <path d="m9 11 3 3L22 4"/>
                </svg>
                <span>Verifikasi Pembayaran</span>
            </a>

            
            {{-- Ronde Arisan --}}
            @php($isActive = request()->routeIs('rounds.*'))
            {{-- FIX: Mengganti route('rounds.round_roulette') yang memerlukan parameter group, ke route index grup. --}}
            <a href="{{ route('rounds.round_index') }}"
               wire:navigate
               class="flex items-center space-x-3 p-3 rounded-xl transition duration-150
               {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
                
                <!-- Icon: Dice (Ronde Arisan) -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                    <path d="M16 8h.01"/>
                    <path d="M8 8h.01"/>
                    <path d="M12 12h.01"/>
                    <path d="M16 16h.01"/>
                    <path d="M8 16h.01"/>
                </svg>
                <span>Ronde Arisan</span>
            </a>

            {{-- Notifikasi --}}
            @php($isActive = request()->routeIs('notification-list'))
            <a href="{{ route('notification-list') }}"
               wire:navigate
               class="flex items-center space-x-3 p-3 rounded-xl transition duration-150
               {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
                
                <!-- Icon: Bell -->
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
               class="flex items-center space-x-3 p-3 rounded-xl transition duration-150
               {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
                
                <!-- Icon: Settings -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 0-.75 2.07l.15.54a2 2 0 0 1-1.13 1.81L3.9 12.32a2 2 0 0 0 0 1.36l.32 1.25a2 2 0 0 1 1.13 1.81l-.15.54a2 2 0 0 0 .75 2.07l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 0 .75-2.07l-.15-.54a2 2 0 0 1 1.13-1.81l1.25-.32a2 2 0 0 0 0-1.36l-.32-1.25a2 2 0 0 1-1.13-1.81l.15-.54a2 2 0 0 0-.75-2.07l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <div class="p-6 border-t border-indigo-50 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} ArisanYuk — Bendahara
    </div>
</aside>