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
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">

        <div class="bg-white rounded-2xl p-6 shadow-sm">

            <p class="text-slate-400">
                Pesanan Bulan Ini
            </p>

            <h2 class="text-2xl font-bold mt-2">
                {{ number_format($ordersThisMonth) }}
            </h2>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm">

            <p class="text-slate-400">
                Pesanan Dalam Proses
            </p>

            <h2 class="text-2xl font-bold mt-2">
                {{ number_format($ordersInProgress) }}
            </h2>

        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm">

            <p class="text-slate-400">
                Pesanan Selesai/Dikirim
            </p>

            <h2 class="text-2xl font-bold mt-2">
                {{ number_format($ordersCompleted) }}
            </h2>

        </div>

        {{-- <div class="bg-white rounded-2xl p-6 shadow-sm">

            <p class="text-slate-400">
                Project Interior
            </p>

            <h2 class="text-3xl font-bold mt-2">
                {{ number_format($totalInterior) }}
            </h2>

        </div> --}}

        <div class="bg-white rounded-2xl p-6 shadow-sm">

            <p class="text-slate-400">
                Customer
            </p>

            <h2 class="text-2xl font-bold mt-2">
                {{ number_format($totalCustomers) }}
            </h2>

        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm">

            <p class="text-slate-400">
                Pendapatan
            </p>

            <h2 class="text-2xl font-bold mt-2">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
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

            {{-- <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
                    <p class="text-slate-500 text-sm">Pesanan Bulan Ini</p>
                    <p class="text-2xl font-semibold mt-2">{{ number_format($ordersThisMonth) }}</p>
                </div>
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
                    <p class="text-slate-500 text-sm">Pesanan Dalam Proses</p>
                    <p class="text-2xl font-semibold mt-2">{{ number_format($ordersInProgress) }}</p>
                </div>
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
                    <p class="text-slate-500 text-sm">Pesanan Selesai/Dikirim</p>
                    <p class="text-2xl font-semibold mt-2">{{ number_format($ordersCompleted) }}</p>
                </div>
            </div> --}}

            <div class="mt-6 h-80 bg-slate-100 rounded-xl p-4">
                <canvas id="revenueChart" class="w-full h-full"></canvas>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const labels = @json($chartLabels);
                    const chartData = @json($chartData);

                    const ctx = document.getElementById('revenueChart').getContext('2d');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Pendapatan (Rp)',
                                data: chartData,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37,99,235,0.08)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 3,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + Number(value).toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>

        </div>


        {{-- Aktivitas --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">

            <h3 class="font-semibold mb-5">
                Aktivitas Terbaru
            </h3>

            <div class="space-y-5">
                @forelse ($recentActivities as $activity)
                    <div>
                        <p class="font-medium">
                            {{ $activity->title }}
                        </p>

                        <span class="text-sm text-gray-500">
                            {{ $activity->subtitle }} • {{ $activity->time }}
                        </span>
                    </div>
                @empty
                    <div>
                        <p class="font-medium text-slate-600">
                            Belum ada aktivitas terbaru.
                        </p>
                    </div>
                @endforelse
            </div>

        </div>

    </div>

</div>
@endsection
