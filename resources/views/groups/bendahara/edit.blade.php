<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
<div class="bg-white shadow-2xl rounded-xl p-8 border border-gray-100">

    <h1 class="text-3xl font-extrabold text-indigo-800 mb-6 border-b pb-2">
        Edit Grup: {{ $name }}
    </h1>

    <!-- Notifikasi (Sukses/Error) -->
    @if (session()->has('success') || session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms x-init="setTimeout(() => show = false, 4000)"
            class="mb-4 p-4 text-sm rounded-lg {{ session()->has('success') ? 'text-green-700 bg-green-100 border-green-500' : 'text-red-700 bg-red-100 border-red-500' }}" role="alert">
            <span class="font-bold">{{ session()->has('success') ? 'Berhasil!' : 'Kesalahan!' }}</span> 
            {{ session('success') ?? session('error') }}
        </div>
    @endif

    @if (!$is_authorized)
        <!-- Tampilan jika tidak berhak mengakses -->
        <div class="p-6 bg-red-50 border-l-4 border-red-400 rounded-lg text-red-800">
            <p class="font-bold">Akses Ditolak</p>
            {{-- Mengganti 'pemiliknya' menjadi 'Bendahara grup' --}}
            <p class="text-sm">Anda tidak diizinkan untuk mengubah detail grup ini karena Anda bukan **Bendahara** grup.</p>
        </div>
    @else
        <!-- Peringatan jika grup sudah aktif -->
        @if ($group_is_active)
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg text-yellow-800">
                <p class="font-bold">Perhatian: Grup Sudah Aktif!</p>
                <p class="text-sm">Nama grup masih dapat diubah. Namun, **Kuota, Iuran, Frekuensi, dan Tanggal Mulai dikunci** dan tidak dapat diubah setelah grup mulai berjalan.</p>
            </div>
        @endif

        <!-- Formulir Edit Grup -->
        <form wire:submit.prevent="updateGroup" class="space-y-6">
            
            <!-- Input Tersembunyi untuk group_pot agar bisa divalidasi -->
            <input type="hidden" wire:model="group_pot">

            <!-- Nama Grup -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Grup Arisan</label>
                <input wire:model.defer="name" type="text" id="name" 
                    class="p-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Deskripsi Grup -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Grup (Opsional)</label>
                <textarea wire:model.defer="description" id="description" rows="3" 
                    class="p-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kuota Anggota -->
                <div>
                    <label for="quota" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Anggota (Kuota)</label>
                    {{-- Gunakan wire:model.live agar summary terupdate, meskipun inputnya didisable saat aktif --}}
                    <input wire:model.live="quota" type="number" id="quota" 
                        class="p-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 
                        {{ $group_is_active ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" 
                        min="2" max="30" {{ $group_is_active ? 'disabled' : '' }}>
                    @error('quota') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <!-- Frekuensi Pembayaran -->
                <div>
                    <label for="frequency" class="block text-sm font-semibold text-gray-700 mb-1">Frekuensi Pembayaran</label>
                    {{-- Gunakan wire:model.live agar summary terupdate. --}}
                    <select wire:model.live="frequency" id="frequency"
                        class="p-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 
                        {{ $group_is_active ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" {{ $group_is_active ? 'disabled' : '' }}>
                        
                        <option value="" disabled selected>Pilih Frekuensi...</option>
                        <option value="weekly">Mingguan</option>
                        <option value="bi-weekly">Dua Mingguan (Bi-Weekly)</option>
                        <option value="monthly">Bulanan</option>
                    </select>
                    @error('frequency') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Iuran per Anggota -->
                <div>
                    <label for="fee_amount" class="block text-sm font-semibold text-gray-700 mb-1">Iuran per Anggota per Periode</label>
                    <div class="relative mt-1 rounded-lg shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        {{-- Gunakan wire:model.live agar summary terupdate --}}
                        <input wire:model.live="fee_amount" type="number" id="fee_amount" 
                            class="p-3 block w-full rounded-lg border-gray-300 pl-10 pr-12 focus:border-indigo-500 focus:ring-indigo-500 
                            {{ $group_is_active ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" 
                            min="1000" max="10000000" {{ $group_is_active ? 'disabled' : '' }}>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500 sm:text-sm">IDR</span>
                        </div>
                    </div>
                    @error('fee_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <!-- Tanggal Mulai -->
                <div>
                    <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai (Opsional)</label>
                    <input wire:model.defer="start_date" type="date" id="start_date" 
                        class="p-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 
                        {{ $group_is_active ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" {{ $group_is_active ? 'disabled' : '' }}>
                    @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Ringkasan Perhitungan (Pot Arisan) -->
            @php
                $frequency_text = [
                    'weekly' => 'Minggu',
                    'bi-weekly' => '2 Minggu',
                    'monthly' => 'Bulan',
                ][$frequency] ?? 'Periode';

                $total_periods = $quota > 0 ? $quota : '...';
            @endphp

            <div class="p-4 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg shadow-inner mt-4">
                <h3 class="text-lg font-bold text-indigo-900 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calculator mr-2"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M8 6h8"/><path d="M8 10h8"/><path d="M8 14h8"/><path d="M12 18h4"/></svg>
                    Ringkasan Perhitungan Arisan
                </h3>
                <ul class="text-sm text-indigo-700 mt-2 space-y-1">
                    <li>
                        Iuran per Anggota: <span class="font-extrabold text-lg text-indigo-800">Rp {{ number_format($fee_amount, 0, ',', '.') }}</span> per {{ $frequency_text }}.
                    </li>
                    <li>
                        Total Periode (Putaran): <span class="font-extrabold">{{ $total_periods }}</span> {{ $frequency_text }}.
                    </li>
                    <li class="pt-2 border-t border-indigo-200 mt-2">
                        Total Uang Arisan (Pot): <span class="font-extrabold text-2xl text-green-700">Rp {{ number_format($group_pot, 0, ',', '.') }}</span>.
                    </li>
                </ul>
                @error('group_pot') 
                    <p class="mt-1 text-sm text-red-600 font-bold">Kesalahan Pot Arisan: {{ $message }}</p> 
                @enderror
            </div>
            <!-- Tombol Submit -->
            <div class="pt-5 flex justify-end space-x-3">
                <a href="{{ route('groups.bendahara.index', $group) }}"
                    class="inline-flex items-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Kembali
                </a>
                <button type="submit" 
                    class="inline-flex justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-3 text-lg font-medium text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:loading.class="bg-indigo-400">
                    <span wire:loading.remove>Simpan Perubahan</span>
                    <span wire:loading>Memperbarui...</span>
                </button>
            </div>
        </form>
    @endif
</div>
</div>