@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <h2 class="text-2xl font-semibold text-slate-800 mb-6">Edit User</h2>

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

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Masukkan nama">
                    @error('name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Masukkan email">
                    @error('email')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="is_active" required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="1" {{ old('is_active', $user->is_active) === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $user->is_active) === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
                    <select name="role" required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super
                            Admin</option>
                    </select>
                    @error('role')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.kelola-admin') }}"
                        class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-3 rounded-xl bg-blue-400 text-white hover:bg-blue-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
