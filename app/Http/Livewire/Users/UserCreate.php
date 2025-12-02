<?php

namespace App\Http\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserCreate extends Component
{
    // Tambahkan $phone
    public $name, $email, $password, $phone;
    // Set default role agar tidak null saat form dimuat
    public $role = 'anggota'; 

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        // Aturan validasi untuk phone: wajib diisi, maksimal 15 karakter, format angka
        'phone' => 'required|string|max:15', 
        'password' => 'required|min:8', // Minimal 8 karakter
        // Hapus 'admin' dari validasi
        'role' => 'required|in:bendahara,anggota', 
    ];

    public function store()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone, // Simpan No. Telepon
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        session()->flash('message', 'Pengguna berhasil ditambahkan!');
        return redirect()->route('users.index'); // Redirect ke halaman list
    }

    public function render()
    {
        // Hapus 'admin' dari daftar role yang dikirim ke view
        return view('users.create', [
            'roles' => ['bendahara', 'anggota']
        ])->layout('layouts.app');
    }
}