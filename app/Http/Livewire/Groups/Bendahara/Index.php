<?php

namespace App\Http\Livewire\Groups\Bendahara;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

class Index extends Component
{
    use WithPagination;

    // --- Properti Existing ---
    // Karena permintaan user hanya ingin search, kita hanya akan memproses grup 'owned' (Bendahara)
    public $tab = 'owned'; 
    public $is_deleting = false;
    public $group_to_delete_id = null;

    // --- Properti BARU untuk Search ---
    // Gunakan wire:model.live.debounce.300ms="search" di Blade
    public $search = ''; 

    // --- Properti BARU untuk Detail View ---
    public $show_detail = false; 
    public $group_detail = null; 

    /**
     * Listener: Reset paginasi saat properti $search diupdate.
     */
    public function updatedSearch()
    {
        $this->resetPage();
        // Sembunyikan detail saat melakukan pencarian baru
        $this->hideDetail(); 
    }

    /**
     * Memuat data grup dan menampilkan panel detail.
     */
    public function showGroupDetail($groupId)
    {
        // Asumsi ada relasi owner (yang sekarang adalah bendahara) dan members
        $group = Group::with('owner', 'members')->find($groupId); 

        if (!$group) {
            session()->flash('error', 'Detail grup tidak ditemukan.');
            return;
        }

        // Set data grup dan tampilkan panel
        $this->group_detail = $group;
        $this->show_detail = true;
    }

    /**
     * Menyembunyikan panel detail.
     */
    public function hideDetail()
    {
        $this->show_detail = false;
        $this->group_detail = null;
    }

    /**
     * Menetapkan ID grup yang akan dihapus dan menampilkan modal.
     */
    public function confirmGroupDeletion($groupId)
    {
        // Menyembunyikan detail view saat akan menghapus
        $this->hideDetail(); 

        $group = Group::find($groupId);

        if (!$group) {
            session()->flash('error', 'Grup tidak ditemukan.');
            return;
        }

        // AUTORISASI KRITIS: HANYA BENDAHARA YANG BOLEH HAPUS (owner_id merujuk pada ID Bendahara)
        if ($group->owner_id !== Auth::id()) { 
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus grup ini karena Anda bukan **bendahara** grup.');
            $this->is_deleting = false;
            return;
        }
        
        $this->group_to_delete_id = $groupId;
        $this->is_deleting = true;
    }

    /**
     * Melakukan proses penghapusan setelah dikonfirmasi.
     */
    public function deleteGroup()
    {
        if (is_null($this->group_to_delete_id)) {
            session()->flash('error', 'Grup yang akan dihapus tidak valid.');
            $this->is_deleting = false;
            return;
        }

        $group = Group::find($this->group_to_delete_id);

        if (!$group) {
            session()->flash('error', 'Grup tidak ditemukan.');
            $this->is_deleting = false;
            return;
        }

        // AUTORISASI FINAL SEBELUM EKSEKUSI
        // Memastikan pengguna yang login adalah bendahara (owner_id)
        if ($group->owner_id !== Auth::id()) {
            session()->flash('error', 'Penghapusan gagal: Anda tidak memiliki izin **Bendahara**.');
            $this->is_deleting = false;
            $this->group_to_delete_id = null;
            return;
        }

        try {
            $groupName = $group->name;
            // Menghapus grup
            $group->delete(); 

            // Reset state
            $this->is_deleting = false;
            $this->group_to_delete_id = null;
            $this->hideDetail();

            session()->flash('success', 'Grup "' . $groupName . '" berhasil dihapus secara permanen.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menghapus grup: ' . $e->getMessage());
        }
    }

    /**
     * Mengambil data grup yang dimiliki Bendahara dan menerapkan filter pencarian.
     */
    public function render()
    {
        // Query dasar: Grup di mana pengguna saat ini adalah Bendahara (owner_id)
        $query = Group::where('owner_id', Auth::id())
                       ->orderBy('created_at', 'desc');

        if (!empty($this->search)) {
            // Filter berdasarkan nama grup yang mengandung string pencarian
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $groups = $query->paginate(10);

        return view('groups.bendahara.index', [
            'groups' => $groups,
        ])->layout('layouts.app');
    }
}