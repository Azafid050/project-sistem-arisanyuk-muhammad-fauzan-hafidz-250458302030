<div class="container mx-auto p-4 sm:p-6 lg:p-8" wire:poll.10s="loadGroupAndMembers">

<!-- Kontainer utama untuk mengatur lebar -->

<div class="max-w-6xl mx-auto">

<!-- Header & Status Grup -->

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 pb-4 border-b border-gray-200">
<h1 class="text-4xl font-extrabold text-gray-900 mb-2">
Kelola Anggota: <span class="text-indigo-600">{{ $group->name ?? 'Grup' }}</span>
</h1>

<!-- Tombol Kembali (Dengan Logika Kondisional) -->
@php
$backRoute = (isset($currentUserGroupRole) && $currentUserGroupRole === 'bendahara')
? route('groups.bendahara.show', $group->id)
: route('groups.show', $group->id);
@endphp

<a href="{{ $backRoute }}" wire:navigate class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 transition duration-150 ease-in-out mt-2 sm:mt-0">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
    Kembali ke Detail Grup
</a>


</div>

<!-- Notifikasi (Success/Error) -->

@if ($message)

<div class="p-4 mb-6 rounded-lg {{ $message['type'] === 'success' ? 'bg-green-100 text-green-700 border-green-300' : 'bg-red-100 text-red-700 border-red-300' }} border-l-4" role="alert">
{{ $message['text'] }}
</div>
@endif

@if (!$isAuthorized)

<div class="bg-red-50 p-6 rounded-2xl shadow-xl border border-red-300">
<p class="text-xl font-semibold text-red-700">Akses Ditolak.</p>
<p class="text-gray-600 mt-2">Anda tidak memiliki peran admin atau bendahara untuk mengelola anggota grup ini.</p>
</div>
@else

<!-- ========================================================= -->

<!-- TAMBAHAN: STATUS KUOTA ANGGOTA -->

<!-- ========================================================= -->

@php
// Menggunakan $group->quota.
$maxMembers = (int) $group->quota;
$totalActiveMembersCount = $approvedMembers->count();
$excludedRoles = ['admin', 'bendahara', 'owner'];
$quotaConsumingCount = $approvedMembers->filter(fn($m) => !in_array($m->role, $excludedRoles))->count();
$isQuotaFull = ($maxMembers > 0) && ($quotaConsumingCount >= $maxMembers);

if ($maxMembers > 0) {
$quotaRemaining = max(0, $maxMembers - $quotaConsumingCount);
$maxMembersText = $maxMembers;
$quotaRemainingText = $quotaRemaining;
$quotaPercentage = min(100, ($quotaConsumingCount / $maxMembers) * 100);
} else {
$maxMembersText = 'Tidak Terbatas';
$quotaRemainingText = 'Tidak Terbatas';
$quotaPercentage = 0;
$isQuotaFull = false;
}

$statusClass = 'bg-green-100 text-green-800';
$barClass = 'bg-green-500';
$statusText = 'Ketersediaan Baik';

if ($maxMembers > 0) {
if ($isQuotaFull) {
$statusClass = 'bg-red-100 text-red-800';
$barClass = 'bg-red-600';
$statusText = 'Kuota Penuh';
} elseif ($quotaPercentage >= 80) {
$statusClass = 'bg-yellow-100 text-yellow-800';
$barClass = 'bg-yellow-500';
$statusText = 'Hampir Penuh';
}
} else {
$statusText = 'Tidak Dibatasi';
$statusClass = 'bg-indigo-100 text-indigo-800';
}
@endphp

<div class="p-6 mb-6 rounded-2xl shadow-lg border border-gray-100 {{ $statusClass }}">
<div class="flex items-center justify-between">
<h2 class="text-xl font-bold">Status Keanggotaan Grup</h2>
<span class="px-3 py-1 text-sm font-semibold rounded-full bg-white shadow-sm">{{ $statusText }}</span>
</div>

<div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4 border-gray-200">
    <div>
        <p class="text-sm font-medium">Semua (Total)</p>
        <p class="text-2xl font-extrabold">{{ $totalActiveMembersCount }}</p>
    </div>
    <div>
        <p class="text-sm font-medium">Batas Kuota Anggota</p>
        <p class="text-2xl font-extrabold">{{ $maxMembersText }}</p>
    </div>
    <div>
        <p class="text-sm font-medium">Sisa Kuota</p>
        <p class="text-2xl font-extrabold">
            {{ $quotaRemainingText }}
        </p>
    </div>
</div>

@if ($maxMembers > 0)
<div class="mt-4">
    <p class="text-sm font-medium mb-1">Penggunaan Kuota Anggota ({{ number_format($quotaPercentage, 1) }}%)</p>
    <div class="w-full bg-gray-200 rounded-full h-2.5">
        <div class="h-2.5 rounded-full {{ $barClass }}" style="width: {{ $quotaPercentage }}%"></div>
    </div>
</div>
@else
<p class="mt-4 text-sm text-gray-600 italic">Nilai batas maksimum 0 atau di bawahnya diartikan sebagai kuota tidak terbatas.</p>
@endif


</div>
<!-- ========================================================= -->

<!-- 1. Permintaan Anggota Tertunda (Pending Requests) -->

<div class="bg-white p-6 rounded-2xl shadow-xl mb-10 border border-gray-100">
<h2 class="text-2xl font-bold text-yellow-700 mb-4 flex items-center">
<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
Permintaan Tertunda (<span class="font-extrabold">{{ $pendingMembers->count() }}</span>)
</h2>

@if ($pendingMembers->isEmpty())
    <div class="text-center p-8 bg-yellow-50 rounded-lg border border-dashed border-yellow-200">
        <p class="text-gray-500 italic">Tidak ada permintaan keanggotaan yang tertunda saat ini.</p>
    </div>
@else
    <ul class="divide-y divide-yellow-200">
        @foreach ($pendingMembers as $member)
            <li class="flex items-center justify-between py-3">
                <div class="flex flex-col">
                    <span class="font-semibold text-gray-800">{{ $member->user->name ?? 'Pengguna [ID: ' . $member->user_id . ']' }}</span>
                    <span class="text-sm text-gray-500">Meminta bergabung pada {{ $member->created_at->diffForHumans() }}</span>
                </div>
                <div class="flex space-x-2">
                    {{-- TOMBOL SETUJUI DENGAN KONDISI KUOTA --}}
                    <button 
                        wire:click="approveMember({{ $member->id }})" 
                        class="px-4 py-2 text-sm font-medium rounded-full transition duration-150 shadow-md 
                            {{ $isQuotaFull ? 'bg-gray-400 text-gray-700 cursor-not-allowed' : 'bg-green-600 text-white hover:bg-green-700' }}"
                        {{ $isQuotaFull ? 'disabled' : '' }}
                        title="{{ $isQuotaFull ? 'Kuota anggota biasa grup sudah penuh.' : 'Setujui Anggota' }}"
                    >
                        Setujui
                    </button>
                    {{-- AKHIR TOMBOL SETUJUI --}}
                    
                    <button wire:click="deleteMember({{ $member->id }})" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-full hover:bg-red-700 transition duration-150 shadow-md">
                        Tolak
                    </button>
                </div>
            </li>
        @endforeach
    </ul>
@endif


</div>

<!-- 2. Anggota Disetujui (Approved Members) -->

<div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100">
<h2 class="text-2xl font-bold text-indigo-700 mb-4 flex items-center">
<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20v-2c0-.656-.126-1.283-.356-1.857zM9 20H4v-2a3 3 0 015-2.236zM9 20v-2c0-.129.023-.254.067-.376zM11 10a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zm-8 4a2 2 0 012 2v2M7 16a2 2 0 012-2h4a2 2 0 012 2v2" /></svg>
Anggota Disetujui (<span class="font-extrabold">{{ $approvedMembers->count() }}</span>)
</h2>

@if ($approvedMembers->isEmpty())
    <div class="text-center p-8 bg-gray-50 rounded-lg border border-dashed border-gray-200">
        <p class="text-gray-500 italic">Grup belum memiliki anggota aktif.</p>
    </div>
@else
    <ul class="divide-y divide-gray-200">
        @foreach ($approvedMembers as $member)
            <li class="flex items-center justify-between py-3">
                <div class="flex flex-col">
                    <span class="font-semibold text-gray-800">{{ $member->user->name ?? 'Pengguna [ID: ' . $member->user_id . ']' }}</span>
                    <span class="text-sm text-indigo-600 font-medium capitalize">{{ $member->role }}</span>
                </div>
                <div class="flex space-x-2">
                    <!-- Tombol hanya muncul jika anggota BUKAN pengelola, atau jika user yang login adalah Super Admin (ID 1) -->
                    @php
                        $isManager = in_array($member->role, ['admin', 'bendahara', 'owner']);
                        $isCurrentUser = $member->user_id === Auth::id();
                        $isSuperAdmin = Auth::id() === 1;
                    @endphp
                    
                    @if (!$isManager || ($isManager && $isSuperAdmin && !$isCurrentUser))
                        {{-- Hanya tampilkan tombol Hapus jika bukan pengelola biasa, atau jika Super Admin (ID 1) menghapus pengelola lain --}}
                        <button wire:click="deleteMember({{ $member->id }})" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-full hover:bg-red-600 transition duration-150 shadow-md">
                            Hapus
                        </button>
                    @else
                        {{-- Tampilkan pesan 'Pengelola' jika anggota tersebut adalah admin/bendahara/owner, atau jika dia adalah user yang sedang login --}}
                        <span class="text-xs text-gray-400 p-2 italic">
                            {{ $isCurrentUser ? 'Anda (Pengelola)' : 'Pengelola Grup' }}
                        </span>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
@endif


</div>

@endif

</div>

</div>