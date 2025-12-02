<?php

namespace App\Http\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserEdit extends Component
{
    // Livewire menggunakan Route Model Binding untuk mengisi $user
    public User $user; 
    
    // Tambahkan $phone
    public $name, $email, $password, $role, $phone;

    // Dipanggil saat komponen diinisialisasi
    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->phone = $user->phone; // Ambil nilai phone
        // Password dikosongkan saat load untuk keamanan
        $this->password = ''; 
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            // Email harus unik, kecuali untuk ID user yang sedang diedit
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'phone' => 'required|string|max:15', // Aturan validasi untuk phone
            'password' => 'nullable|min:8', // Password opsional saat edit
            // Hapus 'admin' dari validasi saat edit
            'role' => 'required|in:bendahara,anggota',
        ];
    }

    public function update()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone, // Update No. Telepon
        ];

        // Hanya update password jika field password diisi
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $this->user->update($data);

        session()->flash('message', 'Pengguna berhasil diperbarui!');
        return redirect()->route('users.index');
    }

    public function render()
    {
        // Hapus 'admin' dari daftar role yang dikirim ke view
        return view('users.edit', [
            'roles' => ['bendahara', 'anggota']
        ])->layout('layouts.app');
    }
}