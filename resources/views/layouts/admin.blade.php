<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])



    <title>@yield('title') • SUMUX</title>

</head>

<body class="bg-slate-100 font-poppins">

    <div class="flex">

        {{-- Sidebar --}}
        <aside class="fixed left-0 top-0 w-72 shrink-0 min-h-screen bg-[#0F172A] text-white p-6">

            <div class="pl-3">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('assets/logo-sumux.png') }}" alt="Logo" class="w-12">
                    <div>
                        <h1 class="text-2xl font-bold">
                            SUMUX
                        </h1>
                        <p class="text-slate-400 text-sm">
                            Property & Interior
                        </p>
                    </div>
                </div>
            </div>


            <nav class="mt-10">

                <a class="block p-3 rounded-xl hover:bg-slate-800" href="{{ route('admin.dashboard') }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                    </div>
                </a>

                <a class="block p-3 mt-2 hover:bg-slate-800 rounded-xl" href="{{ route('admin.pesanan') }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-users"></i>
                        <span>Pesanan</span>
                    </div>
                </a>

                <a class="block p-3 mt-2 hover:bg-slate-800 rounded-xl" href="{{ route('admin.transactions') }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-exchange-alt"></i>
                        <span>Transaksi</span>
                    </div>
                </a>

                <a class="block p-3 mt-2 hover:bg-slate-800 rounded-xl" href="{{ route('admin.laporan') }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-chart-bar"></i>
                        <span>Laporan</span>
                    </div>
                </a>

                <a class="block p-3 mt-2 hover:bg-slate-800 rounded-xl" href="{{ route('admin.cek-ongkir') }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-truck"></i>
                        <span>Cek Ongkir</span>
                    </div>
                </a>

                {{-- Super Admin Only Menu --}}
                @if (auth()->user()->role === 'super_admin')
                    <hr class="my-4 border-slate-700">

                    <a class="block p-3 mt-2 hover:bg-slate-800 rounded-xl" href="{{ route('admin.kelola-admin') }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-users-gear"></i>
                            <span>Kelola Admin</span>
                        </div>
                    </a>

                    <a class="block p-3 mt-2 hover:bg-slate-800 rounded-xl" href="{{ route('admin.settings') }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-cog"></i>
                            <span>Pengaturan</span>
                        </div>
                    </a>
                @endif

            </nav>

        </aside>


        {{-- Content --}}
        <main class="flex-1 ml-72 min-w-0 p-8">

            {{-- TOPBAR --}}
            <div class="bg-white rounded-2xl px-8 py-5 mb-6 shadow-sm">

                <div class="flex items-center justify-between">

                    {{-- Left --}}
                    <div>

                        {{-- Breadcrumb --}}
                        <x-breadcrumb />
                        {{-- @yield('breadcrumb') --}}

                        {{-- Title --}}
                        {{-- <h1 class="text-2xl font-semibold text-slate-800 mt-1">
                            @yield('title')
                        </h1> --}}

                    </div>

                    {{-- Right --}}
                    <div class="flex items-center gap-4">

                        {{-- Quick Action
                        <button class="px-5 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition">

                            <i class="fa-solid fa-plus mr-2"></i>
                            Tambah

                        </button> --}}

                        {{-- Notification --}}
                        {{-- <button class="relative w-12 h-12 rounded-xl hover:bg-slate-100 transition">

                            <i class="fa-regular fa-bell text-lg"></i>

                            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full">
                            </span>

                        </button> --}}

                        {{-- Profile --}}
                        <button id="profile-btn"
                            class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-100 transition relative group">

                            @php
                                $photoPath = auth()->user()->photo;
                                $photoUrl =
                                    $photoPath && file_exists(public_path('storage/' . $photoPath))
                                        ? '/storage/' . $photoPath
                                        : 'https://ui-avatars.com/api/?name=' .
                                            urlencode(auth()->user()->name) .
                                            '&background=071638&color=fff&size=200';
                            @endphp
                            
                            <img src="{{ $photoUrl }}" class="w-10 h-10 rounded-full border shadow object-cover">

                            <div class="text-left">

                                <div class="font-semibold text-sm">
                                    {{ auth()->user()->name }}
                                </div>

                                <div class="text-xs text-slate-500">
                                    {{ auth()->user()->role === 'super_admin' ? 'Owner/Super Admin' : 'Admin' }}
                                </div>

                            </div>

                            <i class="fa-solid fa-chevron-down text-sm text-slate-400"></i>

                        </button>

                        <div id="profile-dropdown"
                            class="hidden absolute right-15 mt-45 w-54 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                            {{-- Dropdown Menu --}}
                            <div class="px-3 py-4 bg-gray-50">
                                <a href="{{ route('admin.profile') }}" class=" text-slate-700">
                                    <div class="w-full text-left px-4 py-3 hover:bg-slate-100 rounded-xl">
                                        <i class="fa-solid fa-user mr-2"></i> Profil
                                    </div>
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" onclick="logoutConfirm(event)"
                                        class="w-full text-left px-4 py-3 hover:bg-slate-100 rounded-xl text-slate-700">
                                        <i class="fa-solid fa-sign-out-alt mr-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const profileBtn = document.getElementById('profile-btn');
        const profileDropdown = document.getElementById('profile-dropdown');

        if (profileBtn) {

            profileBtn.addEventListener('click', function(e) {

                e.stopPropagation();

                profileDropdown.classList.toggle('hidden');

            });

            window.addEventListener('click', function(e) {

                if (
                    !profileBtn.contains(e.target) &&
                    !profileDropdown.contains(e.target)
                ) {

                    profileDropdown.classList.add('hidden');

                }

            });

        }

        function logoutConfirm(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            // const userName = form.closest('tr').querySelector('td:nth-child(2)').innerText;

            Swal.fire({
                title: 'Logout?',
                text: `Anda yakin ingin logout?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</body>

</html>
