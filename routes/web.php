<?php

use App\Http\Livewire\Users\UserEdit;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Users\UserIndex;

use App\Http\Livewire\Groups\GroupEdit;
use App\Http\Livewire\Groups\GroupShow;
use App\Http\Livewire\Rounds\RoundDraw;

use App\Http\Livewire\Users\UserCreate;
use App\Http\Livewire\Groups\GroupIndex;
use App\Http\Livewire\Rounds\RoundIndex;
use App\Http\Livewire\Groups\GroupCreate;
use App\Http\Livewire\Profile\ShowProfile;
use App\Http\Livewire\Rounds\RoundRoulette;

// Bendahara Group Management
use App\Http\Livewire\Charts\DashboardChart;
use App\Http\Livewire\Groups\Bendahara\Edit;
use App\Http\Livewire\Groups\Bendahara\Show;
use App\Http\Livewire\Groups\Manage\Members;

use App\Http\Livewire\GroupMembers\JoinGroup;

// Payment Management
use App\Http\Livewire\Groups\Bendahara\Index;

use App\Http\Livewire\Payments\PaymentCreate;

use App\Http\Livewire\Payments\PaymentVerify;
use App\Http\Livewire\Bendahara\BendaharaEdit;

// Bendahara CRUD
use App\Http\Livewire\Dashboard\UserDashboard;
use App\Http\Livewire\Groups\Bendahara\Create;
use App\Http\Livewire\Bendahara\BendaharaIndex;

use App\Http\Livewire\Dashboard\AdminDashboard;
use App\Http\Livewire\Bendahara\BendaharaCreate;
use App\Http\Livewire\Groups\Anggota\ShowAnggota;

// Group Member/Anggota Views
use App\Http\Livewire\Groups\Anggota\IndexAnggota;
use App\Http\Livewire\Dashboard\BendaharaDashboard;

use App\Http\Livewire\Rounds\Admin\AdminRoundIndex;
use App\Http\Livewire\Notifications\NotificationList;
use App\Http\Livewire\Payments\Admin\IndexPaymentAdmin;
use App\Http\Livewire\Payments\Anggota\IndexPaymentAnggota;
use App\Http\Livewire\Payments\Bendahara\IndexPaymentBendahara;
use App\Http\Livewire\Payments\Anggota\GroupMemberPaymentStatus;
use App\Http\Livewire\Payments\Verifikasi\PaymentVerificationBendahara;


// Protected routes (harus login)
Route::middleware(['auth'])->group(function () {
    // Dashboard per role
    Route::get('/admin/dashboard', AdminDashboard::class)
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::get('/bendahara/dashboard', BendaharaDashboard::class)
        ->middleware('role:bendahara')
        ->name('bendahara.dashboard');

    Route::get('/user/dashboard', UserDashboard::class)
        ->middleware('role:anggota')
        ->name('user.dashboard');

    // ----------------------------------------------------------------------
    // --- ADMIN ROUTES (CRUD Users, Bendahara) ---
    // ----------------------------------------------------------------------
    Route::middleware(['auth', 'role:admin'])->group(function () {
        // User CRUD
        Route::get('/users', UserIndex::class)->name('users.index');
        Route::get('/users/create', UserCreate::class)->name('users.create');
        Route::get('/users/{user}/edit', UserEdit::class)->name('users.edit');
        
        // Bendahara CRUD (Tambahan dari permintaan Anda)
        Route::prefix('bendaharas')->group(function () {
            Route::get('/', Index::class)->name('bendaharas.index');
            Route::get('/create', Create::class)->name('bendaharas.create');
            Route::get('/{bendahara}/edit', Edit::class)->name('bendaharas.edit');
        });
    });


    // ----------------------------------------------------------------------
    // --- GROUP MANAGEMENT ROUTES ---
    // ----------------------------------------------------------------------

    // CRUD Groups (Admin/General)
    Route::get('/groups', GroupIndex::class)->name('groups.index');
    Route::get('/groups/{group}/show', GroupShow::class)->name('groups.show');
    Route::get('/groups/create', GroupCreate::class)->name('groups.create');
    Route::get('/groups/{group}/edit', GroupEdit::class)->name('groups.edit');
    
    // Group Member Management (Admin/Owner)
    Route::get('/groups/{group}/manage/members', Members::class)
        ->name('groups.manage.members');
        
    // Anggota Route: Bergabung dengan Grup (Rute Baru)
    Route::get('/groups/join/{group}', JoinGroup::class)->name('groups.join'); 
        
    // CRUD Groups (Bendahara) - HANYA boleh diakses oleh Bendahara
    Route::middleware(['role:bendahara'])->group(function () {
        Route::prefix('groups/bendahara')->group(function () {
            Route::get('/', Index::class)->name('groups.bendahara.index');
            Route::get('/create', Create::class)->name('groups.bendahara.create'); // Create Group oleh Bendahara
            Route::get('/{group}/edit', Edit::class)->name('groups.bendahara.edit'); // Edit Group oleh Bendahara
            Route::get('/{group}/show', Show::class)->name('groups.bendahara.show'); // Show Group oleh Bendahara
        });
    });
    
    // Group Anggota (user/anggota)
    Route::get('/groups/anggota', IndexAnggota::class)->name('groups.anggota.index_anggota');
    Route::get('/groups/anggota/{group}', ShowAnggota::class)->name('groups.anggota.show_anggota');

    // ----------------------------------------------------------------------
    // --- ROUNDS & PAYMENTS ROUTES ---
    // ----------------------------------------------------------------------

    // Rounds
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('rounds.admin.')->group(function () {
    Route::get('/rounds', AdminRoundIndex::class)->name('admin_round_index');
    });
    Route::get('/rounds', RoundIndex::class)->name('rounds.round_index');
    Route::get('/rounds/round_roulette/{group}', RoundRoulette::class)
        ->name('rounds.round_roulette');

    // Rute untuk Bendahara (Pastikan IndexPaymentBendahara di-import atau didefinisikan)
    Route::middleware(['auth', 'role:bendahara'])
        ->prefix('bendahara') // URL akan menjadi /bendahara/...
        ->name('payments.bendahara.') 
        ->group(function () {
    Route::get('/payments', IndexPaymentBendahara::class) 
        ->name('index_payment_bendahara'); 
        });

    // Rute untuk Admin (Pastikan IndexPaymentAdmin di-import atau didefinisikan)
    Route::middleware(['auth', 'role:admin'])
        ->prefix('admin') // URL akan menjadi /admin/...
        ->name('payments.admin.')
        ->group(function () {
    Route::get('/payments', IndexPaymentAdmin::class) 
        ->name('index_payment_admin');
        });

    // Payments (Anggota)
    Route::get('/payments/anggota', IndexPaymentAnggota::class)->name('payments.anggota.index_payment_anggota');
    Route::get('/payments/anggota/{group}', GroupMemberPaymentStatus::class) 
        ->name('payments.anggota.group_member_payment_status');
    
    // Payments (Verifikasi oleh Bendahara)
    Route::get('/payments/verifikasi/{group}', PaymentVerificationBendahara::class)
        ->name('payments.verifikasi.payment_verification_bendahara');

    // Notifications
    Route::get('/notification', NotificationList::class)->name('notification-list');

    Route::get('/profile', ShowProfile::class)->name('profile.show');
});

Route::get('/', function () {
    return view('landing');
})->name('landing');


// Tambahkan baris ini di paling bawah
require __DIR__.'/auth.php';