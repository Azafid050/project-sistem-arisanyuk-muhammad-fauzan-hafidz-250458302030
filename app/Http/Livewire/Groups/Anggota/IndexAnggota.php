<?php

namespace App\Http\Livewire\Groups\Anggota;

use App\Models\Group;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class IndexAnggota extends Component
{
    use WithPagination;

    public $tab = 'joined'; // Default tab
    public $search = '';

    // PROPERTI UNTUK PENGHAPUSAN
    public $is_deleting = false; 
    public $groupToDeleteId = null;
    public $groupToDeleteName = ''; 

    // Array berisi peran yang dianggap sebagai pengelola (admin level grup)
    protected $managerRoles = ['admin', 'bendahara'];

    /**
     * Tentukan apakah user yang sedang login adalah Super Admin.
     */
    private function isSuperAdmin(int $userId): bool
    {
        // User ID 1 adalah Super Admin yang memiliki hak penuh
        return $userId === 1;
    }
    
    // Metode untuk mengubah tab
    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetPage(); // Reset pagination saat ganti tab
    }

    /**
     * Tentukan apakah user dapat mengelola grup tertentu.
     * Logika ini HARUS memastikan Super Admin selalu kembali TRUE.
     */
    public function canManageGroup(Group $group): bool
    {
        $userId = Auth::id();

        // 1. Super Admin (ID 1) BISA mengelola semuanya (termasuk grup milik bendahara)
        if ($this->isSuperAdmin($userId)) {
            return true;
        }

        // 2. User adalah Owner
        if ($group->owner_id === $userId) {
            return true;
        }

        // 3. User adalah Anggota dengan role 'admin' atau 'bendahara'
        $member = $group->members->first(fn($m) => $m->user_id === $userId);
        
        return $member && in_array($member->role, $this->managerRoles);
    }
    
    // Metode untuk menampilkan modal konfirmasi hapus
    // Nama metode disesuaikan dengan Blade: confirmGroupDeletion
    public function confirmGroupDeletion(int $groupId)
    {
        $group = Group::find($groupId);

        if (!$group) {
            session()->flash('error', 'Grup tidak ditemukan.');
            return;
        }

        // Otorisasi: Hanya tampilkan modal jika user berhak menghapus
        if (!$this->canManageGroup($group)) {
             session()->flash('error', 'Anda tidak memiliki izin untuk menghapus grup ini.');
             return;
        }

        $this->groupToDeleteId = $groupId;
        $this->groupToDeleteName = $group->name; 
        $this->is_deleting = true;
    }

    // Metode untuk menyembunyikan modal konfirmasi hapus
    public function cancelDelete()
    {
        $this->groupToDeleteId = null;
        $this->groupToDeleteName = '';
        $this->is_deleting = false;
    }

    // Metode untuk melakukan penghapusan aktual
    public function deleteGroup()
    {
        if (!$this->groupToDeleteId) {
            $this->cancelDelete();
            return;
        }

        $group = Group::find($this->groupToDeleteId);

        // Re-check Otorisasi sebelum penghapusan
        if (!$group || !$this->canManageGroup($group)) {
             session()->flash('error', 'Aksi dibatalkan. Grup tidak ditemukan atau Anda tidak memiliki izin.');
             $this->cancelDelete();
             return;
        }
        
        $group->delete();
        session()->flash('success', 'Grup "' . $group->name . '" berhasil dihapus.');

        $this->cancelDelete(); 
        $this->resetPage(); 
    }


    /**
     * Mengambil data grup berdasarkan tab aktif dan hak akses.
     */
    public function render()
    {
        $userId = Auth::id(); 
        $isSuperAdmin = $this->isSuperAdmin($userId);
        $query = Group::query();

        $query->with('owner');
        $query->with(['members' => function ($memberQuery) use ($userId) {
            $memberQuery->where('user_id', $userId)->select('user_id', 'group_id', 'role');
        }]);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        if ($this->tab === 'owned') {
            $query->where('owner_id', $userId);

        } elseif ($this->tab === 'managed') {
            
            if ($isSuperAdmin) {
                // Super Admin: Lihat SEMUA grup.
            } else {
                // Manager Grup: Owner ATAU anggota dengan peran manager
                $query->where(function (Builder $q) use ($userId) {
                    $q->where('owner_id', $userId);
                    $q->orWhereHas('members', function (Builder $memberQuery) use ($userId) {
                        $memberQuery->where('user_id', $userId)
                                     ->whereIn('role', $this->managerRoles); 
                    });
                });
            }
        }
        // Tab 'Joined' (Member biasa) tidak ditampilkan di sini karena Anda hanya ingin menampilkan 'owned' dan 'managed'
        // Jika Anda ingin menambahkan kembali tab 'joined' (member biasa), Anda bisa menggunakan ini:
        // elseif ($this->tab === 'joined') {
        //      $query->whereHas('members', function (Builder $memberQuery) use ($userId) {
        //          $memberQuery->where('user_id', $userId)
        //                       ->where('status', 'approved')
        //                       ->whereNotIn('role', $this->managerRoles);
        //      });
        // }
        
        $groups = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('groups.anggota.index_anggota', [
            'groups' => $groups,
            'canManageGroup' => fn(Group $group) => $this->canManageGroup($group),
        ])->layout('layouts.app');
    }
}