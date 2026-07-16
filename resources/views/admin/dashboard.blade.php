@extends('layouts.admin')

@section('content')
@section('title', 'Dashboard')

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-bold text-slate-800">
            Dashboard
        </h1>

        <p class="text-slate-500">
            Selamat datang di panel administrasi SUMUX Property & Interior
        </p>
    </div>


    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

        <div class="bg-white rounded-2xl p-6 shadow-sm">

            <div class="flex justify-between">

                <div>
                    <p class="text-slate-400">
                        Total Property
                    </p>

                    <h2 class="text-3xl font-bold mt-2">
                        156
                    </h2>
                </div>

                <div class="bg-green-100 p-3 rounded-xl">
                    🏢
                </div>

            </div>

        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm">

            <p class="text-slate-400">
                Project Interior
            </p>

            <h2 class="text-3xl font-bold mt-2">
                34
            </h2>

        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm">

            <p class="text-slate-400">
                Customer
            </p>

            <h2 class="text-3xl font-bold mt-2">
                1.245
            </h2>

        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm">

            <p class="text-slate-400">
                Pendapatan
            </p>

            <h2 class="text-3xl font-bold mt-2">
                Rp 420 Jt
            </h2>

        </div>

    </div>


    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Grafik --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm lg:col-span-2">

            <div class="flex justify-between">

                <h3 class="font-semibold">
                    Statistik Transaksi
                </h3>

                <select class="border border-gray-300 rounded-lg px-3 py-2">
                    <option>Bulanan</option>
                </select>

            </div>

            <div class="mt-6 h-80 bg-slate-100 rounded-xl flex items-center justify-center">

                Chart Area

            </div>

        </div>


        {{-- Aktivitas --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">

            <h3 class="font-semibold mb-5">
                Aktivitas Terbaru
            </h3>

            <div class="space-y-5">

                <div>
                    <p class="font-medium">
                        Property Baru
                    </p>

                    <span class="text-sm text-gray-500">
                        5 menit lalu
                    </span>
                </div>

                <div>
                    <p class="font-medium">
                        Customer Baru
                    </p>

                    <span class="text-sm text-gray-500">
                        1 jam lalu
                    </span>
                </div>

                <div>
                    <p class="font-medium">
                        Project Interior Selesai
                    </p>

                    <span class="text-sm text-gray-500">
                        Hari ini
                    </span>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection
