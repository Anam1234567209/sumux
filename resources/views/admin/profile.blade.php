@extends('layouts.admin')

@section('title', 'Profil')

@section('content')
    <div class="space-y-6">

        {{-- Alert Success --}}
        @if (session('success'))
            <div id="success-alert"
                class="bg-green-50 border border-green-200 rounded-2xl p-4 text-green-700 flex items-center gap-3 transition-all duration-500">
                <i class="fas fa-check-circle text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Alert Error --}}
        @if (session('error'))
            <div id="error-alert"
                class="bg-red-50 border border-red-200 rounded-2xl p-4 text-red-700 flex items-center gap-3 transition-all duration-500">
                <i class="fas fa-exclamation-circle text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->has('photo'))
            <div id="error-alert"
                class="bg-red-50 border border-red-200 rounded-2xl p-4 text-red-700 flex items-center gap-3 transition-all duration-500">
                <i class="fas fa-exclamation-circle text-lg"></i>
                {{ $errors->first('photo') }}
            </div>
        @endif

        {{-- Header --}}
        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Profil Saya
            </h1>
            <p class="text-slate-500 mt-1">
                Kelola informasi akun administrator SUMUX.
            </p>
        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid lg:grid-cols-3 gap-6">

                {{-- ========================= --}}
                {{-- FOTO PROFIL --}}
                {{-- ========================= --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">

                    <div class="flex flex-col items-center">

                        @php
                            $photoPath = auth()->user()->photo;
                            $photoUrl =
                                $photoPath && file_exists(public_path('storage/' . $photoPath))
                                    ? asset('storage/' . $photoPath)
                                    : 'https://ui-avatars.com/api/?name=' .
                                        urlencode(auth()->user()->name) .
                                        '&background=071638&color=fff&size=200';
                        @endphp

                        <img src="{{ $photoUrl }}" id="photoPreview"
                            class="w-32 h-32 rounded-full border-4 border-slate-100 shadow object-cover">

                        <h3 class="mt-5 text-xl font-semibold text-slate-800">
                            {{ auth()->user()->name }}
                        </h3>

                        <p class="text-slate-500 text-sm">
                            {{ auth()->user()->role === 'super_admin' ? 'Owner/Super Admin' : 'Admin' }}
                        </p>
                        
                            <label
                                class="mt-6 cursor-pointer bg-[#071638] text-white px-5 py-2 rounded-xl hover:bg-[#0d2457] transition">
                                Upload Foto
                                <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
                            </label>

                        <div class="mt-8 w-full border-t pt-6">

                            <div class="flex justify-between text-sm py-2">
                                <span class="text-slate-500">
                                    Status
                                </span>

                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    Aktif
                                </span>
                            </div>

                            <div class="flex justify-between text-sm py-2">
                                <span class="text-slate-500">
                                    Bergabung
                                </span>

                                <span class="font-medium text-slate-700">
                                    {{ auth()->user()->created_at->format('d M Y') }}
                                </span>
                            </div>

                        </div>

                    </div>

                </div>


                {{-- ========================= --}}
                {{-- FORM --}}
                {{-- ========================= --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Informasi --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">

                        <h2 class="text-xl font-semibold text-slate-800 mb-6">
                            Informasi Akun
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">

                            <div>
                                <label class="block mb-2 font-medium text-slate-700">
                                    Nama Lengkap
                                </label>

                                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-[#071638] focus:border-[#071638]">
                            </div>

                            <div>
                                <label class="block mb-2 font-medium text-slate-700">
                                    Email
                                </label>

                                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-[#071638] focus:border-[#071638]">
                            </div>

                            <div>
                                <label class="block mb-2 font-medium text-slate-700">
                                    Nomor HP
                                </label>

                                <input type="text" name="nomor" placeholder="08xxxxxxxxxx"
                                    value="{{ old('nomor', auth()->user()->nomor) }}"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-[#071638] focus:border-[#071638]">
                            </div>

                            <div>
                                <label class="block mb-2 font-medium text-slate-700">
                                    Username
                                </label>

                                <input type="text" value="{{ auth()->user()->username ?? '-' }}" readonly
                                    class="w-full rounded-xl bg-slate-100 border border-slate-300 px-4 py-3">
                            </div>

                        </div>

                    </div>

                    {{-- Password --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">

                        <h2 class="text-xl font-semibold text-slate-800 mb-6">
                            Ubah Password
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">

                            <div class="md:col-span-2">
                                <label class="block mb-2 font-medium text-slate-700">
                                    Password Baru
                                </label>

                                <input type="password" name="password"
                                    placeholder="Kosongkan jika tidak ingin mengganti password"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-[#071638] focus:border-[#071638]">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-2 font-medium text-slate-700">
                                    Konfirmasi Password
                                </label>

                                <input type="password" name="password_confirmation"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-[#071638] focus:border-[#071638]">
                            </div>

                        </div>

                    </div>

                    {{-- Tombol --}}
                    <div class="flex justify-end">

                        <button type="submit"
                            class="bg-[#071638] hover:bg-[#0d2457] text-white px-8 py-3 rounded-xl font-medium transition">

                            Simpan Perubahan

                        </button>

                    </div>

                </div>

            </div>

        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('error-alert');

            if (successAlert) {
                setTimeout(() => {
                    successAlert.classList.add('opacity-0');

                    setTimeout(() => {
                        successAlert.remove();
                    }, 500);

                }, 3000); // 3 detik
            }

            if (errorAlert) {
                setTimeout(() => {
                    errorAlert.classList.add('opacity-0');

                    setTimeout(() => {
                        errorAlert.remove();
                    }, 500);

                }, 3000); // 3 detik
            }

        });

        const input = document.getElementById('photo');
        const preview = document.getElementById('photoPreview');

        input.addEventListener('change', function() {
            const file = this.files[0];

            if (!file) return;

            preview.src = URL.createObjectURL(file);

            preview.onload = function() {
                URL.revokeObjectURL(preview.src);
            };
        });
    </script>
@endsection
