@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <h2 class="text-2xl font-semibold text-slate-800 mb-6">Tambah User Baru</h2>

            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Masukkan nama">
                    @error('name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="admin@sumux.id">
                    @error('email')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Masukkan password (minimal 8 karakter)">
                    @error('password')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Ulangi password">
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
                    <select name="role" required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin Biasa</option>
                        <option value="super_admin">Super Admin/Owner</option>
                    </select>
                    @error('role')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fa-solid fa-check mr-2"></i> Simpan
                    </button>
                    <a href="{{ route('admin.kelola-admin') }}"
                        class="px-6 py-3 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition">
                        <i class="fa-solid fa-times mr-2"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
