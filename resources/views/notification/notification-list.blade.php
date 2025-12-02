<div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8 font-['Inter'] bg-gray-50 min-h-screen">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menggunakan font Inter */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap');
        .font-inter { font-family: 'Inter', sans-serif; }

        /* Custom style untuk efek hover pada notifikasi */
        .notification-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Animasi fade in untuk notifikasi */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
    </style>

    <h2 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">
        🔔 Pusat Notifikasi Anda
    </h2>
    
    <!-- Header Notifikasi dan Aksi Cepat -->
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-center p-4 bg-white rounded-2xl shadow-xl border border-indigo-100/70">
        <p class="text-xl font-bold text-gray-800 mb-2 sm:mb-0">
            Status: <span class="text-indigo-600 font-extrabold">{{ $unreadCount }}</span> Belum Dibaca
        </p>
        <div class="flex items-center gap-2">
            @if ($unreadCount > 0)
                <!-- Tombol Tandai Semua Dibaca -->
                <button wire:click="markAllAsRead" 
                        class="w-full sm:w-auto px-6 py-2 text-sm text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 font-semibold transition duration-200 shadow-md transform hover:scale-[1.02]">
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>
    </div>

    <!-- Pemberitahuan Success/Error -->
    @if (session()->has('notification_error'))
        <div class="mb-4 p-4 text-sm font-medium text-red-700 rounded-xl bg-red-100 border border-red-300 shadow-sm animate-fade-in">{{ session('notification_error') }}</div>
    @endif
    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm font-medium text-green-700 rounded-xl bg-green-100 border border-green-300 shadow-sm animate-fade-in">{{ session('success') }}</div>
    @endif

    <!-- Daftar Notifikasi -->
    <div class="space-y-4">
        @forelse ($notifications as $notification)
            @php
                $isUnread = !$notification->is_read;
                $bgColor = $isUnread ? 'bg-white border-l-indigo-600 shadow-2xl notification-card' : 'bg-white border-l-gray-300 shadow-lg notification-card';
                $titleColor = $isUnread ? 'text-indigo-700 font-extrabold' : 'text-gray-800 font-bold';
                $iconClass = $isUnread ? 'text-indigo-500 bg-indigo-50/70' : 'text-gray-400 bg-gray-100';
            @endphp

            <div class="p-5 border-l-8 rounded-2xl transition duration-300 animate-fade-in {{ $bgColor }}">
                <div class="flex items-start">
                    <!-- Ikon Notifikasi -->
                    <div class="flex-shrink-0 p-3 rounded-full {{ $iconClass }} mr-4 mt-1">
                        <!-- Placeholder Icon -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.472 6.365 6 8.36 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>

                    <!-- Konten Notifikasi -->
                    <div class="flex-grow">
                        <div class="flex justify-between items-start">
                            <h3 class="text-xl {{ $titleColor }} mb-1">{{ $notification->title }}</h3>
                            <span class="text-xs text-gray-400 mt-1 ml-4 block flex-shrink-0">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-gray-600 text-base mb-3">{{ $notification->message }}</p>
                        
                        <!-- Tombol Aksi -->
                        @if ($isUnread)
                            <button wire:click="markAsRead({{ $notification->id }})" 
                                    class="px-3 py-1.5 text-sm font-semibold text-white bg-indigo-500 rounded-lg hover:bg-indigo-600 transition duration-150 shadow-md transform hover:scale-[1.05] active:scale-95">
                                Tandai Dibaca
                            </button>
                        @else
                            <span class="inline-flex items-center text-xs font-medium text-green-700 bg-green-100 px-3 py-1 rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Sudah Dibaca
                            </span>
                        @endif
                        <!-- Tombol hapus tunggal -->
                        <button wire:click="deleteNotification({{ $notification->id }})" 
                                class="mt-2 px-3 py-1.5 text-sm font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600 transition duration-150 shadow-md">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-2xl shadow-xl border border-gray-200 animate-fade-in">
                <div class="text-6xl text-gray-300 mb-4">✨</div>
                <p class="text-gray-500 text-xl font-medium">Semua jelas! Tidak ada notifikasi baru saat ini.</p>
                <p class="text-gray-400 text-sm mt-2">Cek kembali nanti untuk pembaruan ArisanYuk.</p>
            </div>
        @endforelse
    </div>
</div>