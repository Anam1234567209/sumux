@extends('layouts.admin')

@section('content')
@section('title', 'Laporan')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">

        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Laporan
            </h1>

            <p class="text-slate-500">
                Data aktivitas dan riwayat pengiriman barang
            </p>
        </div>

        <div class="flex gap-3">

            <button
                onclick="window.location.href='{{ route('admin.laporan.export') }}?'+new URLSearchParams(window.location.search).toString()"
                class="px-5 py-3 rounded-xl bg-green-600 text-white hover:bg-green-700">
                <i class="fas fa-file"></i>
                Export

            </button>

        </div>

    </div>

    <!-- Filter -->

    <div class="bg-white rounded-3xl shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('admin.laporan') }}" class="grid md:grid-cols-5 gap-4">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama/alamat..."
                class="border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-400">

            <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                class="border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-400">

            <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
                class="border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-400">

            <select name="kurir_id"
                class="border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Semua Kurir</option>
                @foreach ($kurirList as $k)
                    <option value="{{ $k->id }}" @selected(request('kurir_id') == $k->id)>
                        {{ $k->nama_kurir }}
                    </option>
                @endforeach
            </select>

            <button class="bg-blue-400 text-white rounded-xl hover:bg-blue-700 transition">
                <i class="fas fa-filter"></i>
                Filter
            </button>
        </form>
    </div>


    <!-- Statistik -->

    <div class="grid md:grid-cols-4 gap-5 mb-6">

        <div class="bg-white p-5 rounded-2xl">
            <div class="text-slate-500">Total Transaksi</div>
            <div class="text-3xl font-bold mt-2">
                {{ number_format($totalTransaksi, 0, ',', '.') }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl">
            <div class="text-slate-500">Total Ongkir</div>
            <div class="text-3xl font-bold mt-2">
                Rp{{ number_format($totalOngkir, 0, ',', '.') }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl">
            <div class="text-slate-500">Kurir Aktif</div>
            <div class="text-3xl font-bold mt-2">
                {{ number_format($kurirAktif, 0, ',', '.') }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl">
            <div class="text-slate-500">Rata-rata Ongkir</div>
            <div class="text-3xl font-bold mt-2">
                Rp{{ number_format($rataRataOngkir, 0, ',', '.') }}
            </div>
        </div>

    </div>


    <!-- Table -->

    <div class="bg-white rounded-2xl max-w-full overflow-hidden p-5">
        <div class="overflow-x-auto   border-slate-100 bg-white">
            <table class="w-full min-w-200 table-auto text-sm">
                <thead>

                    <tr class="bg-slate-50/70 text-slate-500 uppercase text-[12px] tracking-wide">

                        <th class="p-4">No</th>

                        <th class="p-4">
                            Tanggal
                        </th>

                        <th class="p-4">
                            Tujuan
                        </th>

                        <th class="p-4">
                            Kurir
                        </th>

                        <th class="p-4">
                            Barang
                        </th>

                        <th class="p-4">
                            Jumlah
                        </th>

                        <th class="p-4">
                            Ongkir
                        </th>

                        <th class="p-4">
                            Status
                        </th>

                        <th class="p-4">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>
                    @forelse ($shipments as $index => $shipment)
                        @php
                            $items = $detailPesanan->get($shipment->pesanan_id, collect());
                            $namaBarang = $items->pluck('nama_item')->implode(', ');
                            $totalQty = $items->sum('jumlah');

                            $pengirimanStyle = [
                                'menunggu' => 'bg-yellow-100 text-yellow-700',
                                'diproses' => 'bg-blue-100 text-blue-700',
                                'dikirim' => 'bg-cyan-100 text-cyan-700',
                                'diterima' => 'bg-green-100 text-green-700',
                                'gagal' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <tr
                            class="border-t border-slate-100 hover:bg-emerald-50/40 transition duration-200 whitespace-nowrap text-center">
                            <td class="p-4">{{ $shipments->firstItem() + $index }}</td>
                            <td class="p-4">
                                {{ \Carbon\Carbon::parse($shipment->tanggal_pesanan)->translatedFormat('d F Y') }}</td>
                            <td class="p-4 text-left max-w-xs truncate" title="{{ $shipment->alamat_pengiriman }}">
                                {{ Illuminate\Support\Str::limit($shipment->alamat_pengiriman, 40) }}
                            </td>
                            <td class="p-4">
                                {{ $shipment->nama_kurir ? $shipment->nama_kurir . ' - ' . $shipment->layanan_kurir : '-' }}
                            </td>
                            <td class="p-4 text-left max-w-xs truncate" title="{{ $namaBarang }}">
                                {{ Illuminate\Support\Str::limit($namaBarang ?: '-', 40) }}
                            </td>
                            <td class="p-4">{{ $totalQty }}</td>
                            <td class="p-4 font-semibold text-left">
                                Rp{{ number_format($shipment->biaya_ongkir, 0, ',', '.') }}
                            </td>
                            <td class="p-4 font-semibold text-center">
                                <span
                                    class="px-3 py-1 rounded-full {{ $pengirimanStyle[$shipment->status_pengiriman] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($shipment->status_pengiriman) }}
                                </span>
                            </td>
                            <td class="p-4">
                                <a href="{{ route('admin.pesanan', ['q' => $shipment->nomor_pesanan]) }}"
                                    class="inline-block p-2 rounded-full bg-slate-100 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                Belum ada data pengiriman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="p-5 flex justify-between items-center">
        <span class="text-gray-500">
            Menampilkan {{ $shipments->firstItem() ?? 0 }}-{{ $shipments->lastItem() ?? 0 }} dari
            {{ $shipments->total() }} data
        </span>

        {{ $shipments->links() }}
    </div>

</div>
@endsection
