<?php

namespace App\Http\Livewire\Groups;

use App\Models\Group;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class GroupIndex extends Component
{
    use WithPagination;

    // Nilai yang diharapkan: 'managed', 'owner' (sesuai UI Blade, 'owner' adalah alias untuk 'joined')
    public $tab = 'managed';
    public $search = '';

    public $is_deleting = false; 
    public $groupToDeleteId = null;
    public $groupToDeleteName = '';

    // Peran lokal yang dianggap memiliki hak manajerial
    protected $managerRoles = ['admin', 'bendahara'];
    
    // Lifecycle hook: Mereset halaman saat properti tertentu berubah
    protected $queryString = ['search' => ['except' => ''], 'tab' => ['except' => 'managed']];

    // Reset halaman saat search atau tab berubah
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'tab'])) {
            $this->resetPage();
        }
    }

    /**
     * Cek apakah user adalah Global Admin/Manager (User dengan role 'admin').
     */
    private function isGlobalAdminOrManager(): bool
    {
        $user = Auth::user();

        // Cek jika user memiliki role 'admin' di tabel users
        if ($user && $user->role === 'admin') {
            return true;
        }
        
        return false; 
    }

    public function setTab(string $tab)
    {
        // Sesuaikan validTabs untuk mencerminkan dua tab di UI
        $validTabs = ['managed', 'owner'];
        if (in_array($tab, $validTabs)) {
            $this->tab = $tab;
        } else {
            $this->tab = 'managed'; 
        }

        $this->resetPage();
    }

    /**
     * Menentukan apakah user saat ini dapat mengedit/menghapus grup.
     * Logika ini HARUS mencakup GLOBAL ADMIN.
     */
    public function canManageGroup(Group $group): bool
    {
        $userId = Auth::id();

        // 1. GLOBAL MANAGER/ADMIN → Akses mutlak ke semua grup
        if ($this->isGlobalAdminOrManager()) {
            return true;
        }

        // 2. OWNER Grup
        if ($group->owner_id === $userId) {
            return true;
        }

        // 3. ADMIN / BENDAHARA GRUP (Diperiksa melalui relasi member)
        $member = $group->members->first(fn($m) => $m->user_id === $userId);
        return $member && in_array($member->role, $this->managerRoles);
    }
    
    /**
     * LOGIKA KUNCI: Menentukan peran yang harus ditampilkan di UI.
     */
    public function getDisplayRole(Group $group): string
    {
        $userId = Auth::id();

        if ($this->canManageGroup($group)) {
            // Jika memiliki hak manajemen, tampilkan 'Owner' atau peran lokal
            if ($group->owner_id === $userId) {
                return 'Owner';
            }
            
            // Jika bukan owner, tapi bisa mengelola (admin/bendahara lokal)
            $member = $group->members->firstWhere('user_id', $userId);
            if ($member) {
                 return ucfirst($member->role);
            }

            // Ini akan mencakup Global Admin yang bukan anggota grup tersebut
            return 'Manager'; 
        }

        // Jika hanya Anggota Biasa, tampilkan peran aslinya
        $member = $group->members->firstWhere('user_id', $userId);
        
        if ($member) {
            return ucfirst($member->role);
        }

        // Default
        return 'Anggota';
    }

    /**
     * Menentukan nama rute 'show' yang benar untuk grup, 
     * diarahkan ke rute manajemen jika user dapat mengelola.
     */
    public function getGroupShowRoute(Group $group): string
    {
        if ($this->canManageGroup($group)) {
            return 'groups.show'; 
        }

        return 'groups.anggota.show'; 
    }

    public function confirmGroupDeletion(int $groupId)
    {
        $group = Group::find($groupId); 

        if (!$group || !$this->canManageGroup($group)) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus grup ini.');
            return;
        }

        $this->groupToDeleteId = $groupId;
        $this->groupToDeleteName = $group->name;
        $this->is_deleting = true;
    }

    public function cancelDelete()
    {
        $this->groupToDeleteId = null;
        $this->groupToDeleteName = '';
        $this->is_deleting = false;
    }

    public function deleteGroup()
    {
        if (!$this->groupToDeleteId) {
            $this->cancelDelete();
            return;
        }

        $group = Group::find($this->groupToDeleteId);

        if (!$group || !$this->canManageGroup($group)) {
            session()->flash('error', 'Aksi dibatalkan. Tidak ada izin.');
            $this->cancelDelete();
            return;
        }

        $group->delete();

        session()->flash('success', 'Grup "' . $group->name . '" berhasil dihapus.');

        $this->cancelDelete();
        $this->resetPage();
    }

    public function render()
    {
        $userId = Auth::id();
        $isGlobalAdminOrManager = $this->isGlobalAdminOrManager();

        $query = Group::query()
            ->with('owner')
            ->with([
                // Muat relasi members untuk membantu cek peran lokal
                'members' => fn($q) =>
                    $q->where('user_id', $userId)
                      ->select('user_id', 'group_id', 'role')
            ]);

        // 1. Terapkan Filter Pencarian
        if ($this->search) {
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
            );
        }

        // 2. Terapkan Filter Tab
        // Jika Global Admin, JANGAN terapkan filter tab, mereka melihat SEMUA grup.
        if (!$isGlobalAdminOrManager) {

            // Tab "Grup yang Saya Kelola" (managed)
            if ($this->tab === 'managed') {
                
                $query->where(function (Builder $q) use ($userId) {
                    $q->where('owner_id', $userId) // Grup yang dibuat user
                      ->orWhereHas('members', fn($m) =>
                        $m->where('user_id', $userId)
                          ->whereIn('role', $this->managerRoles) // Grup tempat user sebagai admin/bendahara
                      );
                });

            } elseif ($this->tab === 'owner') {
                // Tab "Grup Anggota Saya" (Joined Group)
                $query->whereHas('members', fn($m) =>
                    $m->where('user_id', $userId)
                );
            }
        }
        // Jika Global Admin: query akan mengembalikan SEMUA grup (kecuali ada filter pencarian).

        $groups = $query->orderBy('created_at', 'desc')->paginate(10); 

        // Mengarahkan ke file blade yang sesuai
        return view('groups.index', [
            'groups' => $groups,
            'canManageGroup' => fn(Group $group) => $this->canManageGroup($group),
            'getGroupShowRoute' => fn(Group $group) => $this->getGroupShowRoute($group),
            'getDisplayRole' => fn(Group $group) => $this->getDisplayRole($group), 
        ])->layout('layouts.app');
    }
}