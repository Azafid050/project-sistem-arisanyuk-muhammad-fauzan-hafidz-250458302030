<div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="bg-white overflow-hidden shadow-2xl rounded-xl p-8 border border-gray-100">
        <h1 class="text-3xl font-extrabold text-indigo-800 mb-6 border-b pb-2">Buat Grup Arisan Baru</h1>
        <p class="text-gray-500 mb-6">
            Rencanakan arisan Anda! Sebagai pembuat grup, Anda akan otomatis ditetapkan sebagai **Bendahara** dan anggota pertama.
        </p>

        <!-- Notifikasi (Sukses/Error) -->
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                <span class="font-medium">Berhasil!</span> {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
                class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <span class="font-medium">Kesalahan!</span> {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-6">

            <!-- Nama Grup -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Grup Arisan</label>
                <input wire:model.defer="name" type="text" id="name" placeholder="Contoh: Arisan Liburan Akhir Tahun"
                    class="p-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Deskripsi Grup -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Grup (Opsional)</label>
                <textarea wire:model.defer="description" id="description" rows="3" placeholder="Jelaskan tujuan arisan atau aturan khusus..."
                    class="p-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('description') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kuota Anggota -->
                <div>
                    <label for="quota" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Anggota (Kuota)</label>
                    <input wire:model.live="quota" type="number" id="quota" 
                        class="p-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        min="2" max="30">
                    @error('quota') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>
                
                <!-- Frekuensi Pembayaran -->
                <div>
                    <label for="payment_frequency" class="block text-sm font-semibold text-gray-700 mb-1">Frekuensi Pembayaran</label>
                    <select wire:model.defer="payment_frequency" id="payment_frequency"
                        class="p-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="weekly">Mingguan</option>
                        <option value="bi-weekly">Dua Mingguan (Bi-Weekly)</option>
                        <option value="monthly">Bulanan</option>
                    </select>
                    @error('payment_frequency') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Iuran per Anggota -->
                <div>
                    <label for="fee_per_member" class="block text-sm font-semibold text-gray-700 mb-1">Iuran per Anggota per Periode</label>
                    <div class="relative mt-1 rounded-lg shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input wire:model.live="fee_per_member" type="number" id="fee_per_member" 
                            class="p-3 block w-full rounded-lg border-gray-300 pl-10 pr-12 focus:border-indigo-500 focus:ring-indigo-500" 
                            min="1000" max="10000000">
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500 sm:text-sm">IDR</span>
                        </div>
                    </div>
                    @error('fee_per_member') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>
                
                <!-- Tanggal Mulai -->
                <div>
                    <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai (Opsional)</label>
                    <input wire:model.defer="start_date" type="date" id="start_date" 
                        class="p-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('start_date') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Ringkasan Rencana Arisan -->
            @php
                // Pastikan nilai adalah numerik (0) jika kosong atau null
                $fee_numeric = is_numeric($fee_per_member) ? $fee_per_member : 0;
                $pot_numeric = is_numeric($group_pot) ? $group_pot : 0;
                
                $fee_formatted = number_format($fee_numeric, 0, ',', '.');
                $group_pot_formatted = number_format($pot_numeric, 0, ',', '.');
                
                $frequency_text = [
                    'weekly' => 'Minggu',
                    'bi-weekly' => '2 Minggu',
                    'monthly' => 'Bulan',
                ][$payment_frequency] ?? 'Periode';

                $total_periods = $quota > 0 ? $quota : '...';
            @endphp
            
            <div class="p-4 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg shadow-inner mt-4">
                <h3 class="text-lg font-bold text-indigo-900 flex items-center">
                    <!-- Icon kalkulator lucide-react -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calculator mr-2"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M8 6h8"/><path d="M8 10h8"/><path d="M8 14h8"/><path d="M12 18h4"/></svg>
                    Ringkasan Rencana Arisan
                </h3>
                <ul class="text-sm text-indigo-700 mt-2 space-y-1">
                    <!-- BARIS KRITIS: Menampilkan Total Pot Arisan -->
                    <li>
                        Total Pot Arisan (Dimenangkan): <span class="font-extrabold text-2xl text-green-700 block mt-1">Rp {{ $group_pot_formatted }}</span>
                        @error('group_pot') 
                            <!-- Menampilkan pesan error di bawah group_pot -->
                            <p class="text-xs text-red-600 font-bold mt-1">⚠️ {{ $message }}</p> 
                        @enderror
                    </li>
                    <li>
                        Iuran per Anggota: <span class="font-semibold">Rp {{ $fee_formatted }}</span> per {{ $frequency_text }}.
                    </li>
                    <li>
                        Total Periode (Putaran): <span class="font-semibold">{{ $total_periods }}</span> {{ $frequency_text }}.
                    </li>
                </ul>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-5">
                <button type="submit" 
                    class="w-full inline-flex justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-3 text-lg font-medium text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:loading.class="bg-indigo-400">
                    <span wire:loading.remove>Buat Grup Sekarang</span>
                    <span wire:loading>Menyimpan Grup...</span>
                </button>
            </div>
        </form>
    </div>
</div>