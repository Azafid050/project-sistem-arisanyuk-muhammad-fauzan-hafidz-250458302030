<?php

namespace App\Http\Livewire\Notifications;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationList extends Component
{
    public $notifications;
    public $unreadCount = 0;
    public $selectedNotifications = []; // Properti untuk notifikasi yang dipilih

    // Listener dari komponen lain jika ada notifikasi baru
    protected $listeners = ['notificationCreated' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    /**
     * Memuat notifikasi yang belum dibaca untuk pengguna saat ini.
     */
    public function loadNotifications()
    {
        if (Auth::check()) {
            try {
                $this->notifications = Notification::where('user_id', Auth::id())
                                                    ->orderBy('created_at', 'desc')
                                                    ->get();
                $this->unreadCount = $this->notifications->where('is_read', false)->count();
                
                // Reset selected notifications jika tidak ada notifikasi
                if ($this->notifications->isEmpty()) {
                    $this->selectedNotifications = [];
                }
            } catch (\Exception $e) {
                Log::error("Failed to load notifications for user " . Auth::id() . ": " . $e->getMessage());
                $this->notifications = collect();
                $this->unreadCount = 0;
                session()->flash('notification_error', 'Gagal memuat notifikasi.');
            }
        } else {
            $this->notifications = collect();
        }
    }

    /**
     * Menandai notifikasi tertentu sebagai sudah dibaca.
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::where('id', $notificationId)
                                    ->where('user_id', Auth::id())
                                    ->first();

        if ($notification && !$notification->is_read) {
            $notification->update(['is_read' => true]);
            $this->loadNotifications();
            session()->flash('success', 'Notifikasi berhasil ditandai sudah dibaca.');
        }
    }

    /**
     * Menandai semua notifikasi sebagai sudah dibaca.
     */
    public function markAllAsRead()
    {
        if (Auth::check()) {
            Notification::where('user_id', Auth::id())
                        ->where('is_read', false)
                        ->update(['is_read' => true]);
            $this->loadNotifications();
            session()->flash('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
        }
    }

    /**
     * Menghapus notifikasi yang dipilih.
     */
    public function deleteSelectedNotifications()
    {
        if (!empty($this->selectedNotifications)) {
            Notification::whereIn('id', $this->selectedNotifications)
                ->where('user_id', Auth::id())
                ->delete();

            // Reset selected setelah delete
            $this->selectedNotifications = [];
            $this->loadNotifications();
            session()->flash('success', 'Notifikasi yang dipilih berhasil dihapus.');
        }
    }

    /**
     * Menghapus notifikasi tunggal berdasarkan ID.
     */
    public function deleteNotification($notificationId)
    {
        $notification = Notification::where('id', $notificationId)
                                    ->where('user_id', Auth::id())
                                    ->first();

        if ($notification) {
            $notification->delete();
            $this->loadNotifications();
            session()->flash('success', 'Notifikasi berhasil dihapus.');
        } else {
            session()->flash('notification_error', 'Gagal menghapus notifikasi.');
        }
    }

    public function render()
    {
        return view('notification.notification-list')->layout('layouts.app');
    }
}