<div class="p-4 sm:p-6 lg:p-12 max-w-4xl mx-auto min-h-screen pt-12 pb-12" x-data="{ spinning: false }">

    <header class="mb-10 text-center">
        <div class="flex justify-center mb-6">
            {{-- MENGGUNAKAN KEMBALI SVG TROPHY ICON YANG ANDAL UNTUK LOGO HEADER --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                 class="h-28 w-auto text-yellow-500 drop-shadow-xl" {{-- h-28 dan w-auto untuk ukuran yang pas, text-yellow-500 untuk warna emas --}}
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
            </svg>

        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">
            Undian Putaran #{{ $activeRound->round_number ?? 'N/A' }}
        </h1>
        <p class="mt-2 text-lg text-gray-600">
            Grup: <span class="font-semibold text-indigo-700">{{ $group->name }}</span> | Total Hadiah: <span class="font-bold text-green-600">Rp {{ number_format($group->group_pot, 0, ',', '.') }}</span>
        </p>
    </header>

    {{-- Notifikasi --}}
    @if (session()->has('error'))
        <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">{{ session('error') }}</div>
    @endif
    @if (session()->has('info'))
        <div class="mb-4 p-4 text-sm text-blue-800 rounded-lg bg-blue-50" role="alert">{{ session('info') }}</div>
    @endif

    {{-- Card utama --}}
    <div class="bg-white rounded-xl shadow-2xl p-6 md:p-10 border-t-8 border-indigo-600">

        @if (!$rouletteFinished)
            <div class="flex flex-col items-center justify-center">
                
                {{-- Penanda Pemenang (Pointer) --}}
                <div class="relative mb-6">
                    <svg class="absolute -top-10 left-1/2 transform -translate-x-1/2 w-10 h-10 text-red-500 z-10 drop-shadow-lg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 15l-5-5h10l-5 5z"/>
                    </svg>
                    
                    {{-- Canvas Roda Roulette --}}
                    <canvas id="rouletteCanvas" width="500" height="500" class="rounded-full shadow-inner border-4 border-indigo-200"></canvas>
                </div>

                {{-- Tombol Spin --}}
                <button 
                    id="spinButton"
                    @click="spinning = true; startSpin()" 
                    :disabled="spinning || {{ empty($potentialWinners) ? 'true' : 'false' }}"
                    class="w-full max-w-sm py-4 text-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition duration-150 shadow-lg disabled:opacity-50 flex items-center justify-center transform hover:scale-105 active:scale-95"
                >
                    <span x-show="!spinning">Mulai Undian Sekarang!</span>
                    <span x-show="spinning">Roda Berputar...</span>
                    <svg x-show="spinning" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>

                @if (empty($potentialWinners))
                    <p class="text-center text-lg font-semibold text-red-500 mt-4">Semua anggota sudah memenangkan putaran.</p>
                @endif
            </div>
        @else
            {{-- Tampilan Pemenang Akhir --}}
            <div class="text-center py-10">
                <div class="flex justify-center mb-6">
                    {{-- Menggunakan Placeholder untuk memastikan gambar piala pemenang muncul --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                        class="h-28 w-auto text-yellow-500 drop-shadow-xl" {{-- h-28 dan w-auto untuk ukuran yang pas, text-yellow-500 untuk warna emas --}}
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                    </svg>
                </div>
                <p class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Pemenang Putaran #{{ $activeRound->round_number }} Adalah:</p>
                <h2 class="text-5xl md:text-6xl font-extrabold text-indigo-600 mt-4 animate-fade-in-up">{{ $winner['name'] ?? 'Tidak Ada Pemenang' }}</h2>
                <p class="text-xl text-green-600 mt-4 font-bold animate-pulse">🎉 SELAMAT! 🎉</p>
            
                @if ($group->status === 'completed')
                    <p class="text-center text-lg font-semibold text-green-700 mt-8">Semua putaran telah selesai.</p>
                @else
                    <a href="{{ route('payments.verifikasi.payment_verification_bendahara', ['group' => $group->id]) }}"
                        class="mt-8 w-full max-w-xs mx-auto inline-block text-center py-3 px-4 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg transition duration-150 shadow-md transform hover:scale-105 active:scale-95">
                        Lanjut ke Verifikasi Pembayaran Putaran Berikutnya
                    </a>
                @endif
            </div>
        @endif
        
    </div>

    {{-- Tombol Kembali --}}
    <div class="mt-10 text-center">
        <a href="{{ route('bendahara.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 text-base font-semibold flex items-center justify-center space-x-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Dashboard Bendahara</span>
        </a>
    </div>

    {{-- Logic Roda Roulette (JavaScript) --}}
    <script>
        // Data Calon Pemenang dari Livewire
        const potentialWinners = @json($potentialWinners);
        const canvas = document.getElementById('rouletteCanvas');
        const ctx = canvas.getContext('2d');
        const center = canvas.width / 2;
        const radius = center - 5; // Jari-jari roda
        
        let rotation = 0; // Rotasi awal
        let isSpinning = false;
        let finalWinnerId = null;
        
        // Warna untuk setiap segmen roda
        const segmentColors = [
            '#4f46e5', '#10b981', '#f59e0b', '#ef4444', 
            '#06b6d4', '#8b5cf6', '#ec4899', '#3b82f6', 
            '#f97316', '#14b8a6'
        ];

        // Hitung sudut untuk setiap segmen
        const segmentAngle = 2 * Math.PI / potentialWinners.length;

        function drawWheel() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.save();
            ctx.translate(center, center);
            ctx.rotate(rotation);
            
            // Gambar Segmen
            potentialWinners.forEach((winner, index) => {
                const startAngle = index * segmentAngle;
                const endAngle = (index + 1) * segmentAngle;
                
                // Warna
                const colorIndex = index % segmentColors.length;
                ctx.fillStyle = segmentColors[colorIndex];
                ctx.beginPath();
                ctx.moveTo(0, 0);
                ctx.arc(0, 0, radius, startAngle, endAngle);
                ctx.closePath();
                ctx.fill();

                // Garis Pemisah
                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = 3;
                ctx.stroke();

                // Gambar Teks (Nama Pemenang)
                ctx.save();
                ctx.rotate(startAngle + segmentAngle / 2);
                ctx.textAlign = 'right';
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 14px Inter, sans-serif';
                ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
                ctx.shadowBlur = 4;
                
                // Posisikan teks agak jauh dari pusat
                ctx.fillText(winner.name.toUpperCase(), radius - 15, 5, radius - 30);
                
                ctx.restore();
            });

            // Logo/Teks di Tengah Roda
            ctx.beginPath();
            const innerRadius = 50; // DIAMETER DIKECILKAN DARI 70 KE 50
            ctx.arc(0, 0, innerRadius, 0, 2 * Math.PI);
            ctx.fillStyle = '#fef3c7'; // Kuning Muda
            ctx.fill();
            ctx.strokeStyle = '#f59e0b'; // Kuning Tua
            ctx.lineWidth = 5;
            ctx.stroke();

            // Tambahkan Teks "ARISAN" di tengah lingkaran
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#f59e0b'; 
            ctx.font = 'bold 24px Inter, sans-serif'; // Ukuran teks disesuaikan
            ctx.fillText('ARISAN', 0, 0); 
            ctx.restore();

            ctx.restore();
        }
        
        // Fungsi Animasi Putaran
        function spin(duration = 5000) {
            isSpinning = true;
            
            // Tentukan Pemenang Acak di Frontend (Hanya untuk Animasi Visual!)
            const randomIndex = Math.floor(Math.random() * potentialWinners.length);
            const chosenWinner = potentialWinners[randomIndex];
            finalWinnerId = chosenWinner.id;

            // Hitung target sudut: Rotasi penuh + berhenti di tengah segmen pemenang
            const winnerIndex = potentialWinners.findIndex(w => w.id === chosenWinner.id);
            // Sudut segmen pemenang (pusat segmen)
            const targetAngleRad = winnerIndex * segmentAngle + segmentAngle / 2;
            
            // Konversi ke derajat (sudut pointer 90 derajat = 12 jam)
            let targetAngleDeg = (targetAngleRad * 180 / Math.PI) + 90;
            
            // Tambahkan putaran penuh acak (misalnya 5-8 putaran)
            const fullRotations = (Math.floor(Math.random() * 4) + 5) * 360; 
            
            // Sudut akhir total (target putaran akhir)
            const totalTargetRotation = fullRotations + targetAngleDeg;

            // Pastikan sudut putaran saat ini di reset ke 0-360 untuk perhitungan yang mudah
            const currentRotationDeg = (rotation * 180 / Math.PI) % 360;

            // Sudut yang harus diputar dari posisi saat ini
            const rotationNeeded = totalTargetRotation - currentRotationDeg;

            let startTime = null;

            function animate(timestamp) {
                if (!startTime) startTime = timestamp;
                const elapsed = timestamp - startTime;
                const progress = Math.min(elapsed / duration, 1);

                // Fungsi easing (e.g., cubic-out, melambat di akhir)
                const easedProgress = 1 - Math.pow(1 - progress, 3);
                
                // Rotasi saat ini
                const currentRotation = rotationNeeded * easedProgress;
                
                // Tambahkan ke rotasi awal
                rotation = (currentRotation * Math.PI / 180); 
                
                drawWheel();

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    // Animasi selesai
                    isSpinning = false;
                    
                    // Panggil Livewire setelah animasi selesai dan pemenang telah ditentukan
                    console.log('Spin Selesai. Memanggil Livewire dengan Pemenang ID:', finalWinnerId);
                    
                    // Menggunakan $this->getId()
                    window.Livewire.find('{{ $this->getId() }}').call('runRoulette', finalWinnerId);
                }
            }
            
            requestAnimationFrame(animate);
        }

        function startSpin() {
            if (isSpinning) return;
            
            // Panggil fungsi spin utama
            spin();
        }

        // Gambar roda saat komponen dimuat
        if (potentialWinners.length > 0) {
            drawWheel();
        } else {
            // Jika tidak ada calon pemenang, tampilkan pesan di canvas
            ctx.fillStyle = '#f3f4f6';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#9ca3af';
            ctx.font = 'bold 20px Inter, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('SEMUA SUDAH MENANG', center, center);
            document.getElementById('spinButton').disabled = true;
        }

    </script>
    
    {{-- PENTING: Blok <style> harus berada di dalam div root. --}}
    <style>
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-fade-in-up {
            animation: fadeInScale 0.6s ease-out forwards;
        }
        .animate-bounce {
            animation: bounce 1s infinite;
        }
        #rouletteCanvas {
            max-width: 100%;
            height: auto;
        }
    </style>
</div>