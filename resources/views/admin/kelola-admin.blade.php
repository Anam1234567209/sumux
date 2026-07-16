@extends('layouts.admin')

@section('title', 'Data Pengguna')

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

        {{-- Header --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <h1 class="text-3xl font-bold text-slate-800">
                    Data Pengguna
                </h1>

                <p class="text-slate-500 mt-1">
                    Kelola akun administrator dan staff yang dapat mengakses dashboard SUMUX.
                </p>
            </div>

            <a href="{{ route('admin.users.create') }}"
                class="bg-[#071638] text-white px-6 py-3 rounded-2xl hover:opacity-90 transition">

                + Tambah Pengguna

            </a>

        </div>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            <div class="bg-white rounded-2xl shadow-sm p-6">

                <p class="text-slate-400">
                    Total Pengguna
                </p>

                <h2 class="text-3xl font-bold mt-2">
                    {{ $users->count() }}
                </h2>

            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">

                <p class="text-slate-400">
                    Administrator
                </p>

                <h2 class="text-3xl font-bold mt-2">
                    {{ $users->where('role', 'admin')->count() }}
                </h2>

            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">

                <p class="text-slate-400">
                    Staff
                </p>

                <h2 class="text-3xl font-bold mt-2">
                    0
                </h2>

            </div>

        </div>

        {{-- Table --}}
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden p-5">

            <div class="overflow-x-auto border-slate-100 bg-white">

                <div class="p-6 border-b border-slate-100">

                    <div class="flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">

                        <div class="relative w-full lg:w-96">

                            <input type="text" placeholder="Cari nama atau email..."
                                class="w-full border border-slate-300 rounded-2xl px-5 py-3 outline-none focus:ring-2 focus:ring-blue-400">

                        </div>

                        <select class="border border-slate-300 rounded-2xl px-5 py-3">

                            <option>Semua Role</option>
                            <option>Administrator</option>
                            <option>Staff</option>

                        </select>

                    </div>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-slate-50">

                            <tr class="text-left text-sm text-slate-500">

                                <th class="p-5">No</th>
                                <th class="p-5">Nama</th>
                                <th class="p-5">Email</th>
                                <th class="p-5">Role</th>
                                <th class="p-5">Status</th>
                                <th class="p-5">Terakhir Login</th>
                                <th class="p-5 text-center">Aksi</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($users as $user)
                                <tr
                                    class="border-t border-slate-100 hover:bg-emerald-50/40 transition duration-200 whitespace-nowrap">

                                    <td class="p-5">{{ $loop->iteration }}</td>

                                    <td class="p-5 font-medium">
                                        {{ $user->name }}
                                    </td>

                                    <td class="p-5">
                                        {{ $user->email }}
                                    </td>

                                    <td class="p-5">

                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs capitalize">

                                            {{ $user->role }}

                                        </span>

                                    </td>

                                    <td class="p-5">

                                        @if ($user->is_active)
                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">

                                                Aktif

                                            </span>
                                        @else
                                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">

                                                Nonaktif

                                            </span>
                                        @endif

                                    </td>

                                    <td class="p-5">
                                        {{ $user->last_login ? $user->last_login->format('d M Y, H:i') : 'Belum pernah login' }}
                                    </td>

                                    <td class="p-5">

                                        <div class="flex justify-center gap-2">

                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="px-3 py-2 rounded-xl bg-yellow-100 hover:bg-yellow-200 text-amber-600">
                                                <i class="fas fa-edit"></i>
                                                Edit

                                            </a>

                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="deleteConfirm(event)"
                                                    class="px-3 py-2 rounded-xl bg-red-100 text-red-600 hover:bg-red-200">
                                                    <i class="fas fa-trash"></i>
                                                    Hapus

                                                </button>
                                            </form>

                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function deleteConfirm(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            const userName = form.closest('tr').querySelector('td:nth-child(2)').innerText;

            Swal.fire({
                title: 'Hapus Pengguna?',
                text: `Anda akan menghapus pengguna "${userName}". Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

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
    </script>

@endsection
