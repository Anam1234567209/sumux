@extends('layouts.admin')

@section('page', 'Transaksi')
@section('title', 'Data Transaksi')

@section('content')

    {{-- Statistik --}}
    <div class="flex justify-between items-center mb-6">

        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Transaksi
            </h1>

            <p class="text-slate-500">
                Data aktivitas dan riwayat transaksi
            </p>
        </div>

        <div class="flex gap-3">

            {{-- <button class="px-5 py-3 rounded-xl bg-blue-400 borde text-white hover:bg-blue-500 transition">
                <i class="fas fa-print"></i>
                Export PDF

            </button> --}}

            <button
                onclick="window.location.href='{{ route('admin.transactions.export') }}?'+new URLSearchParams(window.location.search).toString()"
                class="px-5 py-3 rounded-xl bg-green-600 text-white hover:bg-green-700 transition">
                <i class="fas fa-file"></i>
                Export

            </button>

        </div>

    </div>


    {{-- Table --}}
    <div class="space-y-6">

        {{-- Header --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.transactions') }}" class="grid md:grid-cols-6 gap-4">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari transaksi..."
                    class="border border-gray-300 rounded-xl px-4 py-3 w-full">

                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                    class="border border-gray-300 rounded-xl px-4">

                <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
                    class="border border-gray-300 rounded-xl px-4">

                <select name="status" class="border border-gray-300 rounded-xl px-4">
                    <option value="">Status</option>
                    @foreach ($statusPesanan as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
                <select name="per_page" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-xl px-4 outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="8" @selected($perPage === '8')>8 Baris</option>
                    <option value="25" @selected($perPage === '25')>25 Baris</option>
                    <option value="50" @selected($perPage === '50')>50 Baris</option>
                    <option value="all" @selected($perPage === 'all')>Tampilkan Semua</option>
                </select>

                <button class="bg-blue-400 text-white rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
        </div>


        {{-- Table --}}
        <div class="bg-white rounded-2xl max-w-full overflow-hidden p-5">

            <div class="overflow-x-auto   border-slate-100 bg-white">

                <table class="w-full min-w-200 table-auto text-sm">

                    <thead>

                        <tr class="bg-slate-50/70 text-slate-500 uppercase text-[12px] tracking-wide">

                            <th class="p-5">No.</th>
                            <th class="p-5">Invoice</th>
                            <th class="p-5">Customer</th>
                            <th class="p-5">Tanggal</th>
                            <th class="p-5">Total</th>
                            <th class="p-5">Status</th>
                            <th class="p-5">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($transactions as $trx)
                            <tr
                                class="border-t border-slate-100 hover:bg-emerald-50/40 transition duration-200 whitespace-nowrap">

                                <td class="p-5 text-center">
                                    {{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}
                                </td>

                                <td class="p-5">
                                    <div class="font-semibold">
                                        {{ $trx->nomor_pesanan }}
                                    </div>
                                </td>

                                <td class="p-5">
                                    <div>
                                        <div class="font-medium">
                                            {{ $trx->nama_pelanggan }}
                                        </div>
                                        <div class="text-sm text-slate-400">
                                            {{ $trx->pelanggan_email ?? '-' }}
                                        </div>
                                    </div>
                                </td>

                                <td class="p-5 text-center">
                                    {{ \Carbon\Carbon::parse($trx->tanggal_pesanan)->translatedFormat('d F Y') }}
                                </td>

                                <td class="p-5 font-semibold text-blue-600 text-center">
                                    Rp {{ number_format($trx->total_tagihan, 0, ',', '.') }}
                                </td>

                                <td class="p-5 text-center">
                                    @php
                                        $trxStatusStyle = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'produksi' => 'bg-blue-100 text-blue-700',
                                            'finishing' => 'bg-indigo-100 text-indigo-700',
                                            'selesai' => 'bg-green-100 text-green-700',
                                            'dikirim' => 'bg-cyan-100 text-cyan-700',
                                            'dibatalkan' => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span
                                        class="px-4 py-2 rounded-full text-sm {{ $trxStatusStyle[$trx->status_pesanan] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($trx->status_pesanan) }}
                                    </span>
                                </td>

                                <td class="p-5 align-center">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.pesanan', ['q' => $trx->nomor_pesanan]) }}"
                                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-center inline-block">
                                            <i class="fa-solid fa-eye"></i> Lihat
                                        </a>

                                        <form action="{{ route('admin.pesanan.destroy', $trx->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus data transaksi ini? Data pesanan terkait juga akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="openDeleteTransaction(event)"
                                                class="px-4 py-2 rounded-xl bg-rose-50 text-rose-600 opacity-70 hover:bg-rose-100 hover:opacity-100 transition cursor-pointer">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    Belum ada data transaksi.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        <div class="p-5 flex justify-between items-center">
            <span class="text-gray-500">
                Menampilkan {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} dari
                {{ $transactions->total() }} data
            </span>

            {{ $transactions->links() }}
        </div>

    </div>
    <script>
        const transactionModal = document.getElementById('transactionModal');
        const transactionForm = document.getElementById('transactionForm');
        const transactionMethodInput = document.getElementById('transactionMethod');
        const transactionIdInput = document.getElementById('transactionId');
        const transactionModalTitle = document.getElementById('transactionModalTitle');

        function openDeleteTransaction(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            const transactionNumber = form.closest('tr').querySelector('td:nth-child(1)').innerText;

            Swal.fire({
                title: 'Hapus Data Transaksi?',
                text: `Anda akan menghapus transaksi "${transactionNumber}". Seluruh data pesanan yang terkait juga akan terhapus..`,
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
    </script>

@endsection
