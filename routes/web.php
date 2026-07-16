<?php

use App\Http\Controllers\OngkirController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LaporanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


// login route
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return Auth::check()
        ? redirect()->route('admin.dashboard')
        : view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $remember = $request->boolean('remember');

    if (! Auth::attempt($credentials, $remember)) {
        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    // Update last_login
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $user->update([
        'last_login' => now(),
    ]);

    $request->session()->regenerate();

    return redirect()->intended(route('admin.dashboard'));
})->name('login.submit');

// logout route
Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');


// api cek ongkir route
Route::post('/api/cek-ongkir', [OngkirController::class, 'hitung'])
    ->name('api.cek-ongkir')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/api/ongkir/destinations', [OngkirController::class, 'destinations'])
    ->name('api.ongkir.destinations');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active'])->group(function () {

    // Dashboard route
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Pesanan routes
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan');
    Route::get('/pesanan/{id}/edit', [PesananController::class, 'edit'])->name('pesanan.edit');
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    Route::put('/pesanan/{id}', [PesananController::class, 'update'])->name('pesanan.update');
    Route::patch('/pesanan/{id}/quick-update', [PesananController::class, 'quickUpdate'])->name('pesanan.quick-update');
    Route::post('/pesanan/{id}/payments', [PesananController::class, 'addPayment'])->name('pesanan.payments.store');
    Route::delete('/pesanan/{id}', [PesananController::class, 'destroy'])->name('pesanan.destroy');

    // Transactions route
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');

    // Profile routes
    Route::get('/profile', function () {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    })->name('profile');

    // Ongkir Routes
    Route::get('/cek-ongkir', [OngkirController::class, 'index'])->name('cek-ongkir');
    Route::post('/cek-ongkir/hitung', [OngkirController::class, 'hitung'])->name('hitung-ongkir');

    // Update profile route
    Route::put('/profile', function (Request $request) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email,' . $user->id],
                'nomor' => ['nullable', 'string', 'max:20'],
                'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            ],
            [
                'photo.image' => 'File yang dipilih harus berupa gambar.',
                'photo.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
                'photo.max' => 'Ukuran foto maksimal 2 MB.',
                'name.required' => 'Nama wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email tersebut sudah digunakan.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
            ]
        );

        $photoPath = $user->photo;
        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $photoPath = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'nomor' => $request->input('nomor', $request->input('phone')),
            'photo' => $photoPath,
            'password' => $request->filled('password') ? bcrypt($request->password) : $user->password,
        ]);

        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui');
    })->name('profile.update');


    // laporan route
    // laporan route
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');

    // Super Admin Only Routes
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/kelola-admin', function () {
            $users = \App\Models\User::where('role', '!=', 'super_admin')->get();
            return view('admin.kelola-admin', compact('users'));
        })->name('kelola-admin');

        // Create user route
        Route::get('/users/create', function () {
            return view('admin.users.create');
        })->name('users.create');

        // user store route
        Route::post('/users', function (Request $request) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role' => ['required', 'in:admin,super_admin'],
            ]);

            \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);

            return redirect()->route('admin.kelola-admin')->with('success', 'User berhasil ditambahkan');
        })->name('users.store');

        // Edit user route
        Route::get('/users/{user}/edit', function (\App\Models\User $user) {
            return view('admin.users.edit', compact('user'));
        })->name('users.edit');

        // Update user route
        Route::put('/users/{user}', function (\App\Models\User $user, Request $request) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email,' . $user->id],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'is_active' => ['required', 'boolean'],
                'role' => ['required', 'in:admin,super_admin'],
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? bcrypt($request->password) : $user->password,
                'is_active' => $request->is_active,
                'role' => $request->role,
            ]);

            return redirect()->route('admin.kelola-admin')->with('success', 'User berhasil diperbarui');
        })->name('users.update');

        // Delete user route
        Route::delete('/users/{user}', function (\App\Models\User $user) {
            $user->delete();
            return redirect()->route('admin.kelola-admin')->with('success', 'User berhasil dihapus');
        })->name('users.destroy');

        // Settings route
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings');
    });
});
