@extends('layouts.admin')

@section('content')
@section('title', 'Pesanan')

@php
    $formatRupiah = fn($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $statusStyle = [
        'pending' => 'bg-yellow-50 text-yellow-700',
        'produksi' => 'bg-blue-50 text-blue-600',
        'finishing' => 'bg-indigo-50 text-indigo-600',
        'selesai' => 'bg-emerald-50 text-emerald-600',
        'dikirim' => 'bg-cyan-50 text-cyan-600',
        'dibatalkan' => 'bg-rose-50 text-rose-600',
    ];
    $packingStyle = [
        'belum' => 'bg-orange-50 text-orange-600',
        'proses' => 'bg-blue-50 text-blue-600',
        'selesai' => 'bg-emerald-50 text-emerald-600',
    ];
    $statusPembayaranStyle = [
        'belum_bayar' => 'bg-rose-50 text-rose-600',
        'DP' => 'bg-yellow-50 text-yellow-700',
        'lunas' => 'bg-emerald-50 text-emerald-600',
    ];
@endphp

<div class="space-y-6">
    @if (session('success'))
        <div id="success-alert"
            class="bg-green-50 border border-green-200 rounded-2xl p-4 text-green-700 flex items-center gap-3 transition-all duration-500">
            <i class="fas fa-check-circle text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div id="error-alert"
            class="bg-red-50 border border-red-200 rounded-2xl p-4 text-red-700 flex items-center gap-3 transition-all duration-500">
            <i class="fas fa-exclamation-circle text-lg"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Data Pesanan
            </h1>

            <p class="text-slate-500">
                Kelola pesanan customer SUMUX Property & Interior
            </p>
        </div>

        <div class="flex gap-3">
            <button class="px-5 py-3 bg-blue-400 text-white rounded-xl hover:bg-blue-500">
                <i class="fas fa-print"></i> Cetak
            </button>
            <button type="button" data-open-order-modal
                class="px-5 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700">
                <i class="fas fa-plus"></i>
                Tambah Pesanan
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.pesanan') }}" class="grid md:grid-cols-6 gap-4">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari customer..."
                class="border border-gray-300 rounded-xl px-4 py-3 w-full outline-none focus:ring-2 focus:ring-blue-400">

            <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                class="border border-gray-300 rounded-xl px-4 outline-none focus:ring-2 focus:ring-blue-400">

            <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
                class="border border-gray-300 rounded-xl px-4 outline-none focus:ring-2 focus:ring-blue-400">

            <select name="status"
                class="border border-gray-300 rounded-xl px-4 outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Semua Status</option>
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

    <div class="bg-white rounded-2xl max-w-full overflow-hidden p-5">
        <div class="overflow-x-auto border-slate-100 bg-white">
            <table class="w-full min-w-450 table-auto text-sm">
                <thead>
                    <tr class="bg-slate-50/70 text-slate-500 uppercase text-[12px] tracking-wide">
                        <th class="px-5 py-5">
                            <input type="checkbox">
                        </th>
                        <th class="px-5 py-5">No. Pesanan</th>
                        <th class="px-5 py-5">Tanggal</th>
                        <th class="px-5 py-5">Customer</th>
                        <th class="px-5 py-5">Alamat</th>
                        <th class="px-5 py-5">Detail Produk</th>
                        <th class="px-5 py-5">Preview</th>
                        <th class="px-5 py-5">Progress</th>
                        <th class="px-5 py-5">Ongkir</th>
                        <th class="px-5 py-5">Tagihan</th>
                        <th class="px-5 py-5">Masuk</th>
                        <th class="px-5 py-5">Kurang</th>
                        <th class="px-5 py-5">Bank</th>
                        <th class="px-5 py-5">Status Pembayaran</th>
                        <th class="px-5 py-5">Packing</th>
                        <th class="px-5 py-5">Dok. Bayar</th>
                        <th class="px-5 py-5">Ekspedisi</th>
                        <th class="px-5 py-5">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pesanan as $order)
                        @php
                            $details = $detailPesanan->get($order->id, collect());
                            $preview = $details->firstWhere('path_preview');
                            $totalTagihan = (float) $order->total_tagihan;
                            $sisaTagihan = max((float) $order->sisa_tagihan, 0);
                            $statusPembayaranOtomatis =
                                $sisaTagihan <= 0
                                    ? 'lunas'
                                    : ($totalTagihan <= 0 || $sisaTagihan >= $totalTagihan
                                        ? 'belum_bayar'
                                        : 'DP');
                        @endphp
                        <tr
                            class="border-t border-slate-100 hover:bg-emerald-50/40 transition duration-200 whitespace-nowrap">
                            <td class="px-5 py-5 text-center">
                                <input type="checkbox" class="rounded border-slate-300" />
                            </td>

                            <td class="px-5 py-6">
                                {{ $order->nomor_pesanan }}
                            </td>

                            <td class="px-5 py-6">
                                {{ \Carbon\Carbon::parse($order->tanggal_pesanan)->translatedFormat('d M Y') }}
                            </td>

                            <td class="px-5 py-6">
                                <div>
                                    <p class="font-semibold text-slate-700">
                                        {{ $order->nama_pelanggan }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $order->nomor_whatsapp }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $order->pesanan_email ?: $order->pelanggan_email ?: '-' }}
                                    </p>
                                </div>
                            </td>

                            <td class="px-5 py-6 min-w-52 whitespace-normal text-slate-500">
                                {{ $order->alamat_pengiriman ?: '-' }}
                            </td>

                            <td class="px-5 py-6 min-w-56 whitespace-normal">
                                @forelse ($details->take(3) as $detail)
                                    <div class="font-medium text-slate-700">
                                        {{ $detail->nama_item }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        {{ $detail->jumlah }} {{ $detail->satuan }} -
                                        {{ $formatRupiah($detail->subtotal) }}
                                    </div>
                                @empty
                                    <span class="text-slate-400">Belum ada detail</span>
                                @endforelse

                                @if ($details->count() > 3)
                                    <div class="text-xs text-slate-400 mt-1">
                                        +{{ $details->count() - 3 }} item lain
                                    </div>
                                @endif
                            </td>

                            <td class="px-5 py-6 min-w-40">
                                @php
                                    $previews = $details->pluck('path_preview')->filter();
                                @endphp

                                @if ($previews->isNotEmpty())
                                    <div class="flex -space-x-5">
                                        @foreach ($previews->take(3) as $previewPath)
                                            <img src="{{ $previewPath }}"
                                                onclick="window.open('{{ $previewPath }}', '_blank')"
                                                title="{{ $details->firstWhere('path_preview', $previewPath)->nama_item }}"
                                                class="w-16 h-16 rounded-2xl object-cover ring-1 ring-slate-100 cursor-pointer" />
                                        @endforeach
                                        @if ($previews->count() > 3)
                                            <div
                                                class="w-16 h-16 rounded-2xl bg-slate-100 ring-1 ring-slate-200 flex items-center justify-center text-xs font-semibold text-slate-500">
                                                +{{ $previews->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <img src="https://placehold.co/90x90?text=SUMUX"
                                        class="w-16 h-16 rounded-2xl object-cover ring-1 ring-slate-100" />
                                @endif
                            </td>

                            <td class="px-5 py-6">
                                <select
                                    class="quick-edit-select inline-flex px-3 py-2 rounded-full border-0 font-medium cursor-pointer {{ $statusStyle[$order->status_pesanan] ?? 'bg-slate-50 text-slate-600' }}"
                                    data-order-id="{{ $order->id }}" data-field="status_pesanan"
                                    onchange="quickUpdateOrder(this)">
                                    @foreach ($statusPesanan as $status)
                                        <option value="{{ $status }}"
                                            {{ $order->status_pesanan === $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="px-5 py-6">
                                {{ $formatRupiah($order->biaya_ongkir) }}
                            </td>

                            <td class="px-5 py-6 font-semibold">
                                {{ $formatRupiah($order->total_tagihan) }}
                                <div class="text-xs text-slate-400 font-normal">
                                    Produk {{ $formatRupiah($order->total_tagihan - $order->biaya_ongkir) }} + Ongkir
                                    {{ $formatRupiah($order->biaya_ongkir) }}
                                </div>
                            </td>

                            <td class="px-5 py-6 text-emerald-600 font-semibold">
                                <div class="flex items-center gap-2">
                                    <span>{{ $formatRupiah($order->total_dibayar) }}</span>
                                    <button type="button"
                                        onclick="openQuickPay(this, {{ $order->id }}, {{ $order->pembayaran_bank_id ?? 'null' }})"
                                        class="w-6 h-6 shrink-0 rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100 text-sm font-bold leading-none">
                                        +
                                    </button>
                                </div>
                            </td>

                            <td class="px-5 py-6 text-rose-500 font-semibold" id="sisa-tagihan-{{ $order->id }}">
                                {{ $formatRupiah($order->sisa_tagihan) }}
                            </td>

                            <td class="px-5 py-6">
                                @php
                                    $bankListForOrder = $paymentBanks
                                        ->get($order->id, collect())
                                        ->pluck('nama_bank')
                                        ->filter()
                                        ->unique();
                                @endphp
                                <div class="px-3 py-2 rounded-xl bg-slate-50 inline-flex items-center gap-1.5"
                                    @if ($bankListForOrder->count() > 1) title="Riwayat bank: {{ $bankListForOrder->implode(', ') }}" @endif>
                                    <span>{{ $order->nama_bank ?: ucfirst($order->metode_pembayaran ?: '-') }}</span>
                                    @if ($bankListForOrder->count() > 1)
                                        <span
                                            class="px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-600 text-xs font-semibold">
                                            +{{ $bankListForOrder->count() - 1 }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-5 py-6">
                                <span
                                    class="px-4 py-2 rounded-full {{ $statusPembayaranStyle[$statusPembayaranOtomatis] ?? 'bg-slate-50 text-slate-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $statusPembayaranOtomatis)) }}
                                </span>
                            </td>

                            <td class="px-5 py-6">
                                <select
                                    class="quick-edit-select px-3 py-2 rounded-full border-0 cursor-pointer {{ $packingStyle[$order->status_packing] ?? 'bg-slate-50 text-slate-600' }}"
                                    data-order-id="{{ $order->id }}" data-field="status_packing"
                                    onchange="quickUpdateOrder(this)">
                                    @foreach ($statusPacking as $status)
                                        <option value="{{ $status }}"
                                            {{ $order->status_packing === $status ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="px-5 py-6 min-w-40 ">
                                @php
                                    $proofsForOrder = $paymentProofs->get($order->id, collect());
                                @endphp
                                @if ($proofsForOrder->isNotEmpty())
                                    <div class="flex -space-x-3">
                                        @foreach ($proofsForOrder->take(3) as $proof)
                                            <img src="{{ $proof->path_bukti_bayar }}"
                                                onclick="window.open('{{ $proof->path_bukti_bayar }}', '_blank')"
                                                title="{{ $proof->nama_bank }} - {{ $formatRupiah($proof->jumlah_bayar) }}"
                                                class="w-12 h-12 rounded-2xl object-cover ring-2 ring-white cursor-pointer">
                                        @endforeach
                                        @if ($proofsForOrder->count() > 3)
                                            <div
                                                class="w-12 h-12 rounded-2xl bg-slate-100 ring-2 ring-white flex items-center justify-center text-xs font-semibold text-slate-500">
                                                +{{ $proofsForOrder->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="px-5 py-6">
                                <select
                                    class="quick-edit-select px-3 py-2 rounded-xl border-0 bg-slate-50 cursor-pointer"
                                    data-order-id="{{ $order->id }}" data-field="kurir_id"
                                    onchange="quickUpdateOrder(this)">
                                    <option value="" {{ !$order->kurir_id ? 'selected' : '' }} disabled hidden>
                                        Pilih Ekspedisi</option>
                                    @foreach ($kurir as $k)
                                        <option value="{{ $k->id }}"
                                            {{ (int) $order->kurir_id === $k->id ? 'selected' : '' }}>
                                            {{ $k->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="px-5 py-6">
                                <div class="flex gap-2">
                                    <button type="button" onclick="window.openOrderModal({{ $order->id }})"
                                        class="px-4 py-2 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.pesanan.destroy', $order->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="openDeleteOrder(event)"
                                            class="px-4 py-2 rounded-xl bg-rose-50 text-rose-600 opacity-70 hover:bg-rose-100 hover:opacity-100 transition cursor-pointer">
                                            <i class="fa-solid fa-trash"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="px-5 py-12 text-center text-slate-500">
                                Belum ada data pesanan. Jalankan seeder untuk mengisi data dummy.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="p-5 flex flex-wrap gap-4 justify-between items-center">
        <span class="text-gray-500">
            Menampilkan {{ $pesanan->firstItem() ?? 0 }}-{{ $pesanan->lastItem() ?? 0 }} dari {{ $pesanan->total() }}
            data
        </span>

        {{ $pesanan->links() }}
    </div>
</div>

{{-- Popup Tambah Pesanan (modal) --}}
<div id="orderModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 px-3 py-4 sm:px-4">
    <div class="mx-auto flex h-full max-w-5xl items-center justify-center">
        <div class="w-full max-h-[90vh] overflow-y-auto rounded-3xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h3 id="orderModalTitle" class="text-xl font-semibold text-slate-800">Tambah Pesanan</h3>
                    <p class="text-sm mb-1 text-slate-500">Isi data pesanan dengan lengkap dan benar agar dapat
                        diproses dengan baik.</p>
                    <p class="text-xs text-red-400">* Kolom yang ditandai dengan bintang (*) wajib diisi</p>
                </div>
                <button type="button" onclick="closeOrderModal()"
                    class="rounded-full p-2 text-slate-500 hover:bg-slate-100">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="orderForm" method="POST" class="px-6 py-5" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="POST" id="orderMethod">
                <input type="hidden" name="order_id" id="orderId" value="">

                <div class="grid gap-5 lg:grid-cols-2">
                    <div class="space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Nama Pelanggan <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" required
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Nomor Pesanan</label>
                                <input type="text" name="nomor_pesanan" type="text"
                                    value="{{ $nextNomorPesanan }}" readonly
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none bg-slate-100">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">No. WhatsApp <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="nomor_whatsapp" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" name="customer_email"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Alamat Pengiriman <span
                                    class="text-red-500">*</span></label>
                            <textarea name="alamat_pengiriman" required
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none"></textarea>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Tanggal Pesanan <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_pesanan" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Status Pesanan <span
                                        class="text-red-500">*</span></label>
                                <select name="status_pesanan" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                                    @foreach ($statusPesanan as $status)
                                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Produk <span
                                class="text-red-500">*</span></label>
                        <input type="hidden" name="total_tagihan" value="0">
                        <div id="productRows" class="space-y-3.5 mb-5"></div>

                        <button type="button" onclick="addProductRow()"
                            class="mb-5 inline-flex items-center gap-2 rounded-xl border border-dashed border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 hover:border-blue-400 hover:text-blue-600">
                            <i class="fa-solid fa-plus"></i> Tambah Produk
                        </button>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Status Pembayaran</label>
                                <input type="text" id="statusPembayaranPreview" value="Belum bayar" readonly
                                    class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-600 focus:border-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Status Packing</label>
                                <select name="status_packing" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                                    @foreach ($statusPacking as $status)
                                        <option value="{{ $status }}">
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Total Dibayar </label>
                                <input type="text" name="total_dibayar" min="0" step="1000"
                                    default="0" placeholder="Total Dibayar"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Sisa Tagihan</label>
                                <input type="text" name="sisa_tagihan" min="0" step="1000"
                                    placeholder="Sisa Tagihan"
                                    class="sisa-tagihan w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 focus:border-blue-500 focus:outline-none"
                                    readonly>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Bank</label>
                                <select name="metode_pembayaran" required
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                                    @foreach ($metodePembayaran as $bank)
                                        <option value="{{ $bank->id }}">
                                            {{ $bank->nama_bank }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Kurir/Ekspedisi</label>
                                <select name="kurir_id"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                                    <option value="" selected disabled hidden>Pilih Ekspedisi</option>
                                    @foreach ($kurir as $k)
                                        <option value="{{ $k->id }}">
                                            {{ $k->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-slate-700">Biaya Ongkir</label>
                                <input type="text" name="biaya_ongkir" value="0" placeholder="Biaya Ongkir"
                                    class="ongkir w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 border-t border-slate-200 pt-6 sm:col-span-2">
                        <div class="grid gap-6 lg:grid-cols-2">

                            <!-- Preview Produk -->
                            {{-- <div>
                                <label class="mb-1 block text-base font-semibold text-slate-800">
                                    Preview Produk (Foto Barang)
                                </label>

                                <p class="mb-3 text-sm text-slate-500">
                                    Upload foto produk sebagai preview pesanan.
                                </p>

                                <label
                                    class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-8 transition hover:border-blue-500 hover:bg-blue-50">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 h-10 w-10 text-slate-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                            d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 4v11m0 0l-4-4m4 4l4-4" />
                                    </svg>

                                    <span class="text-sm font-medium text-slate-700">
                                        Klik untuk upload atau drag & drop
                                    </span>

                                    <span class="mt-1 text-xs text-slate-500">
                                        JPG, PNG (Maks. 5 MB)
                                    </span>

                                    <input type="file" name="foto_produk" accept="image/*" class="hidden"
                                        id="fotoProduk">
                                </label>

                                <div class="mt-4">
                                    <p class="mb-2 text-sm font-medium text-slate-600">
                                        Preview
                                    </p>

                                    <img id="previewProduk"
                                        src="https://placehold.co/280x170/e2e8f0/64748b?text=Belum+Ada+Foto"
                                        class="h-40 w-auto rounded-xl border border-slate-300 object-cover">
                                </div>
                            </div> --}}

                            <!-- Bukti Transaksi -->
                            <div>
                                <label class="mb-1 block text-base font-semibold text-slate-800">
                                    Bukti Transaksi (Foto Pembayaran)
                                </label>

                                <p class="mb-3 text-sm text-slate-500">
                                    Upload bukti pembayaran dari pelanggan.
                                </p>

                                <label
                                    class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-8 transition hover:border-blue-500 hover:bg-blue-50">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 h-10 w-10 text-slate-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                            d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 4v11m0 0l-4-4m4 4l4-4" />
                                    </svg>

                                    <span class="text-sm font-medium text-slate-700">
                                        Klik untuk upload atau drag & drop
                                    </span>

                                    <span class="mt-1 text-xs text-slate-500">
                                        JPG, PNG (Maks. 5 MB)
                                    </span>

                                    <input type="file" name="bukti_transaksi" accept="image/*" class="hidden"
                                        id="buktiTransaksi">
                                </label>

                            </div>
                            <div>
                                <div class="mt-4">
                                    <p class="mb-2 text-sm font-medium text-slate-600">
                                        Preview
                                    </p>

                                    <img id="previewBukti"
                                        src="https://placehold.co/280x170/e2e8f0/64748b?text=Belum+Ada+Foto"
                                        class="h-40 w-auto rounded-xl border border-slate-300 object-cover">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5">
                    <button type="button" onclick="closeOrderModal()"
                        class="rounded-xl border border-slate-300 px-5 py-3 text-slate-600 hover:bg-slate-100">Batal</button>
                    <button type="submit"
                        class="rounded-xl bg-green-600 px-5 py-3 font-medium text-white hover:bg-green-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Quick Pay Popover --}}
<div id="quickPayPopover" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 px-4">
    <div class="w-full max-w-xs rounded-3xl bg-white shadow-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="space-y-1">
                <p class="text-base font-semibold text-slate-800">Tambah Pembayaran</p>
                <p class="text-xs text-slate-500">Isi sesuai dengan jumlah yang ingin dibayar dan metode pembayarannya
                </p>
            </div>
            <button type="button" onclick="closeQuickPay()"
                class="rounded-full p-1.5 text-slate-500 hover:bg-slate-100">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <input type="hidden" id="quickPayOrderId" value="">

        <label class="mb-1 block text-xs font-medium text-slate-500">Jumlah Bayar</label>
        <input type="text" id="quickPayAmount" placeholder="Jumlah Bayar"
            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 mb-3 text-sm focus:border-blue-500 focus:outline-none">

        <label class="mb-1 block text-xs font-medium text-slate-500">Bank/Metode</label>
        <select id="quickPayBank"
            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 mb-5 text-sm focus:border-blue-500 focus:outline-none">
            @foreach ($metodePembayaran as $bank)
                <option value="{{ $bank->id }}">{{ $bank->nama_bank }}</option>
            @endforeach
        </select>
        <label class="mb-1 block text-xs font-medium text-slate-500">Bukti Transfer (opsional)</label>
        <input type="file" id="quickPayBukti" accept="image/*"
            class="w-full text-sm text-slate-600 mb-5 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">

        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeQuickPay()"
                class="px-4 py-2.5 text-sm rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50">
                Batal
            </button>
            <button type="button" onclick="submitQuickPay()"
                class="px-4 py-2.5 text-sm rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
                Simpan
            </button>
        </div>
    </div>
</div>

<script>
    const orderModal = document.getElementById('orderModal');
    const orderForm = document.getElementById('orderForm');
    const orderMethodInput = document.getElementById('orderMethod');
    const orderIdInput = document.getElementById('orderId');
    const orderModalTitle = document.getElementById('orderModalTitle');

    function openDeleteOrder(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        const orderNumber = form.closest('tr').querySelector('td:nth-child(2)').innerText;

        Swal.fire({
            title: 'Hapus Pesanan?',
            text: `Anda akan menghapus pesanan "${orderNumber}". Tindakan ini tidak dapat dibatalkan.`,
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
