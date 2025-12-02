<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
<div class="bg-white shadow-2xl rounded-xl p-8 border border-gray-100">

    {{-- Pesan Flash Session (Jika ada pesan dari Livewire JoinGroup) --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif
    
    <h1 class="text-3xl font-extrabold text-indigo-800 mb-6 border-b pb-2">
        Administrasi Grup: {{ $name }}
    </h1>

    {{-- Peringatan Grup Aktif --}}
    @if ($group_is_active)
        <div class="mb-6 p-4 bg-indigo-50 border-l-4 border-indigo-400 rounded-lg text-indigo-800">
            <p class="font-bold">Status Grup: Sedang Berjalan (Aktif)</p>
            <p class="text-sm">Grup ini sudah aktif. Semua detail perhitungan dikunci untuk memastikan konsistensi selama periode arisan.</p>
        </div>
    @endif

    {{-- Detail Grup Read-Only --}}
    <div class="space-y-6">
        
        {{-- Nama Grup --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Grup Arisan</label>
            <div @class([
                'p-3 rounded-lg font-medium border border-gray-300',
                'bg-gray-200 text-gray-600 cursor-not-allowed' => $group_is_active,
                'bg-gray-100 text-gray-800' => !$group_is_active,
            ])>
                {{ $name }}
            </div>
        </div>

        {{-- Deskripsi Grup --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Grup</label>
            <div @class([
                'p-3 rounded-lg border border-gray-300',
                'bg-gray-200 text-gray-600 cursor-not-allowed' => $group_is_active,
                'bg-gray-100 text-gray-800' => !$group_is_active,
            ])>
                {{ $description }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Kuota Anggota --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Anggota (Kuota)</label>
                <div @class([
                    'p-3 rounded-lg font-medium border border-gray-300',
                    'bg-gray-200 text-gray-600 cursor-not-allowed' => $group_is_active,
                    'bg-gray-100 text-gray-800' => !$group_is_active,
                ])>
                    {{ $quota }} Anggota
                </div>
            </div>
            
            {{-- Frekuensi Pembayaran --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Frekuensi Pembayaran</label>
                <div @class([
                    'p-3 rounded-lg font-medium border border-gray-300',
                    'bg-gray-200 text-gray-600 cursor-not-allowed' => $group_is_active,
                    'bg-gray-100 text-gray-800' => !$group_is_active,
                ])>
                    {{ $this->getFrequencyText() }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Iuran per Anggota --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Iuran per Anggota per Periode</label>
                <div @class([
                    'p-3 rounded-lg font-medium border border-gray-300',
                    'bg-gray-200 text-gray-600 cursor-not-allowed' => $group_is_active,
                    'bg-gray-100 text-gray-800' => !$group_is_active,
                ])>
                    {{ $this->formatRupiah($fee_amount) }}
                </div>
            </div>
            
            {{-- Tanggal Mulai --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai</label>
                <div @class([
                    'p-3 rounded-lg font-medium border border-gray-300',
                    'bg-gray-200 text-gray-600 cursor-not-allowed' => $group_is_active,
                    'bg-gray-100 text-gray-800' => !$group_is_active,
                ])>
                    {{ $start_date ?: 'Belum Ditetapkan' }} 
                    @if ($group_is_active) 
                        (Aktif)
                    @endif
                </div>
            </div>
        </div>

        {{-- Ringkasan Perhitungan (Pot Arisan) --}}
        <div class="p-4 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg shadow-inner mt-4">
            <h3 class="text-lg font-bold text-indigo-900 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calculator mr-2"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M8 6h8"/><path d="M8 10h8"/><path d="M8 14h8"/><path d="M12 18h4"/></svg>
                Ringkasan Perhitungan Arisan
            </h3>
            <ul class="text-sm text-indigo-700 mt-2 space-y-1">
                <li>
                    Iuran per Anggota: <span class="font-extrabold text-lg text-indigo-800">{{ $this->formatRupiah($fee_amount) }}</span> per <span id="summary-frequency">{{ $this->getFrequencyText() }}</span>.
                </li>
                <li>
                    Total Periode (Putaran): <span class="font-extrabold">{{ $quota }}</span> {{ $this->getFrequencyText() }}.
                </li>
                <li class="pt-2 border-t border-indigo-200 mt-2">
                    Total Uang Arisan (Pot): <span class="font-extrabold text-2xl text-green-700">{{ $this->formatRupiah($group_pot) }}</span>.
                </li>
            </ul>
        </div>
    </div>
    
    <hr class="my-8 border-gray-200" />
    
    {{-- AREA KOMPONEN JOIN/LEAVE GROUP (DIKONDISIKAN UNTUK PEMILIK) --}}
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Aksi Keanggotaan</h3>
        
        {{-- Menggunakan $current_user_is_bendahara sesuai komponen Livewire --}}
        @if ($current_user_is_bendahara)
            {{-- Tampilkan pesan informasi khusus untuk bendahara/owner --}}
            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg text-yellow-800">
                <p class="font-bold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-banknote mr-2"><rect width="20" height="12" x="2" y="6" rx="3"/><path d="M12 18V6"/><path d="M12 21a3 3 0 0 1-3-3"/><path d="M12 3a3 3 0 0 1 3 3"/></svg>
                    Anda adalah Bendahara Grup ini.
                </p>
                <p class="text-sm mt-1">Sebagai bendahara, Anda adalah penanggung jawab dan otomatis menjadi anggota. Kelola grup dan anggota melalui tombol aksi di bawah.</p>
            </div>
        @else
            <!-- MEMANGGIL KOMPONEN LIVEWIRE JOIN GROUP -->
            <!-- Komponen ini hanya ditampilkan jika pengguna BUKAN bendahara/owner grup -->
            @livewire('group-members.join-group', ['groupId' => $group->id])
        @endif
    </div>
    {{-- AKHIR AREA KOMPONEN JOIN/LEAVE GROUP --}}


    {{-- Tombol Aksi --}}
    <div class="pt-4 flex justify-end space-x-4">

        {{-- Tombol Kembali --}}
        <a href="{{ route('groups.bendahara.index') }}" wire:navigate class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-md text-base font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            Kembali
        </a>

        {{-- Tombol Kelola Anggota (Selalu ada untuk Bendahara) --}}
        @if ($current_user_is_bendahara)
            <a href="{{ route('groups.manage.members', $group->id) }}" wire:navigate class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-md text-base font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition duration-150 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-round mr-2"><path d="M18 21a8 8 0 0 0-16 0"/><circle cx="10" cy="8" r="5"/><path d="M22 21a8 8 0 0 0-16 0"/><circle cx="10" cy="8" r="5"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Kelola Anggota
            </a>
        @endif

        {{-- Tombol Edit/Administrasi --}}
        
    </div>
</div>
</div>