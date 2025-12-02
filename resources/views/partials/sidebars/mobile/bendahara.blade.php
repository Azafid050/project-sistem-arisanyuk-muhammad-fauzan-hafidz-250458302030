<!-- Sidebar Mobile Bendahara -->
<div class="p-4">
    <nav class="space-y-2">
        {{-- Dashboard Bendahara --}}
        @php($isActive = request()->routeIs('bendahara.dashboard'))
        <a href="{{ route('bendahara.dashboard') }}"
            wire:navigate
            class="flex items-center space-x-3 p-3 rounded-lg transition duration-150
            {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
            🏠 <span>Dashboard</span>
        </a>

        {{-- Kelola Grup --}}
        @php($isActive = request()->routeIs('groups.*'))
        <a href="{{ route('groups.bendahara.index') }}"
            wire:navigate
            class="flex items-center space-x-3 p-3 rounded-lg transition duration-150
            {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
            👥 <span>Kelola Grup</span>
        </a>

        {{-- Verifikasi Pembayaran --}}
        @php($isActive = request()->routeIs('payments.verify'))
        <a href="{{ route('payments.bendahara.index_payment_bendahara') }}"
            wire:navigate
            class="flex items-center space-x-3 p-3 rounded-lg transition duration-150
            {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
            ✅ <span>Verifikasi Pembayaran</span>
        </a>

        {{-- Ronde Arisan --}}
        @php($isActive = request()->routeIs('rounds.*'))
        <a href="{{ route('rounds.round_index') }}"
            wire:navigate
            class="flex items-center space-x-3 p-3 rounded-lg transition duration-150
            {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
            🎲 <span>Ronde Arisan</span>
        </a>

        {{-- Notifikasi --}}
        @php($isActive = request()->routeIs('notification-list'))
        <a href="{{ route('notification-list') }}"
            wire:navigate
            class="flex items-center space-x-3 p-3 rounded-lg transition duration-150
            {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
            🔔 <span>Notifikasi</span>
        </a>

        {{-- Profil --}}
        @php($isActive = request()->routeIs('profile.show'))
        <a href="{{ route('profile.show') }}"
            wire:navigate
            class="flex items-center space-x-3 p-3 rounded-lg transition duration-150
            {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-sm' : 'hover:bg-indigo-50 hover:text-indigo-700' }}">
            ⚙️ <span>Profil</span>
        </a>
    </nav>

    <div class="mt-6 border-t pt-4 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} ArisanYuk — Bendahara
    </div>
</div>