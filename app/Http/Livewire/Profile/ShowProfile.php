<?php

namespace App\Http\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads; // BARU: Trait untuk upload file
use Illuminate\Support\Facades\Storage; // BARU: Untuk mengelola file storage

class ShowProfile extends Component
{
    use WithFileUploads; // Menggunakan Livewire WithFileUploads

    // State untuk form profile information
    public $name = '';
    public $email = '';
    public $phone = '';
    public $user;
    public $role = '';
    
    // BARU: State untuk upload foto
    public $photo;
    public $photo_status = null;
    
    // State untuk form password update
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';
    
    // Notifikasi
    public $password_status = null;
    public $profile_status = null;


    /**
     * Memuat data pengguna saat komponen diinisialisasi
     */
    public function mount()
    {
        $this->user = Auth::user();
        if ($this->user) {
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->phone = $this->user->phone;
            $this->role = ucfirst($this->user->role);
        }
    }

    /**
     * Aturan validasi untuk pembaruan informasi profil
     */
    protected function rules()
    {
        if (!$this->user) {
            return [];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($this->user->id),
            ],
            'phone' => ['nullable', 'string', 'max:15'], 
        ];
    }
    
    /**
     * BARU: Aturan validasi untuk foto profil
     */
    protected function photoRules()
    {
        return [
            // Memastikan itu adalah gambar dan maksimal 2MB
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'], 
        ];
    }

    /**
     * Memperbarui informasi profil (nama, email, dan phone)
     */
    public function updateProfileInformation()
    {
        $this->validate();

        $this->user->forceFill([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ])->save();

        $this->profile_status = 'Informasi profil berhasil diperbarui.';
        session()->flash('profile_status', $this->profile_status);
        $this->dispatch('profile-updated'); 
    }
    
    /**
     * BARU: Memperbarui foto profil
     */
    public function updateProfilePhoto()
    {
        $this->validate($this->photoRules());
        
        // Hapus foto lama jika ada dan pastikan path-nya ada
        if ($this->user->profile_photo_path) {
            Storage::disk('public')->delete($this->user->profile_photo_path);
        }

        // Simpan foto baru ke direktori 'profile-photos' pada disk 'public'
        // Livewire secara otomatis menangani nama file yang unik.
        $path = $this->photo->store('profile-photos', 'public');

        // Perbarui path di database
        $this->user->forceFill([
            'profile_photo_path' => $path, // Anda harus memastikan kolom ini ada di tabel users
        ])->save();

        // Reset state dan tampilkan notifikasi
        $this->photo = null;
        $this->photo_status = 'Foto profil berhasil diperbarui.';
        session()->flash('photo_status', $this->photo_status);
        $this->dispatch('photo-updated');
    }


    /**
     * Aturan validasi untuk pembaruan kata sandi
     */
    protected function passwordRules()
    {
        return [
            'current_password' => ['required', 'string'], 
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Memperbarui kata sandi
     */
    public function updatePassword()
    {
        $this->validate($this->passwordRules());

        if (!Auth::guard('web')->validate([
            'email' => $this->user->email,
            'password' => $this->current_password,
        ])) {
            $this->reset(['password', 'password_confirmation']);
            throw ValidationException::withMessages([
                'current_password' => ['Kata sandi saat ini tidak cocok dengan catatan kami.'],
            ]);
        }
        
        $this->user->password = Hash::make($this->password);
        $this->user->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);
        
        $this->password_status = 'Kata sandi berhasil diperbarui.';
        session()->flash('password_status', $this->password_status);

        Auth::login($this->user);
    }

    public function render()
    {
        return view('profile.show-profile')
            ->layout('layouts.app', ['header' => 'Profil Pengguna']);
    }
}