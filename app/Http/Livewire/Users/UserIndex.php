<?php

namespace App\Http\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage(); // Reset halaman saat melakukan pencarian baru
    }

    public function delete(User $user)
    {
        // Guard: Mencegah admin menghapus akunnya sendiri
        if (auth()->id() === $user->id) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $user->delete();
        session()->flash('message', 'Pengguna berhasil dihapus.');
    }

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
                     ->orWhere('email', 'like', '%' . $this->search . '%')
                     ->orderBy('id', 'desc')
                     ->paginate(10);

        return view('users.index', [
            'users' => $users,
        ])->layout('layouts.app');
    }
}