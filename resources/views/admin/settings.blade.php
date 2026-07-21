@extends('layouts.admin')

@section('content')
@section('title', 'Pengaturan')

<div class="space-y-6">
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Pengaturan</h1>
            <p class="text-slate-500">Kelola data bank, progres, packing, dan ekspedisi untuk operasional admin</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <aside class="lg:col-span-4 xl:col-span-3">
            <div class="rounded-2xl bg-white p-4 shadow-sm">
                <nav class="space-y-2">
                    <a href="#bank"
                        class="flex items-center gap-3 rounded-xl bg-blue-50 p-3 font-medium text-blue-700">
                        <i class="fa-solid fa-building-columns w-5"></i> Bank
                    </a>
                    {{-- <a href="#progres"
                        class="flex items-center gap-3 rounded-xl p-3 text-slate-600 transition hover:bg-slate-50">
                        <i class="fa-solid fa-spinner w-5"></i> Progres
                    </a> --}}
                    <a href="#packing"
                        class="flex items-center gap-3 rounded-xl p-3 text-slate-600 transition hover:bg-slate-50">
                        <i class="fa-solid fa-box-open w-5"></i> Packing
                    </a>
                    <a href="#ekspedisi"
                        class="flex items-center gap-3 rounded-xl p-3 text-slate-600 transition hover:bg-slate-50">
                        <i class="fa-solid fa-truck-fast w-5"></i> Ekspedisi
                    </a>
                </nav>
            </div>
        </aside>

        <div class="space-y-6 lg:col-span-8 xl:col-span-9">
            <section id="bank" class="rounded-2xl bg-white p-6 shadow-sm">
                <div class="mb-6 flex flex-col gap-1">
                    <h2 class="text-xl font-semibold text-slate-800">Bank</h2>
                    <p class="text-slate-500">Tambah dan kelola daftar bank yang tersedia untuk pembayaran</p>
                </div>

                <form action="{{ route('admin.settings.banks.store') }}" method="POST"
                    class="mb-6 grid gap-4 md:grid-cols-3">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Nama Bank</label>
                        <input type="text" name="nama_bank" required placeholder="Contoh: Bank BRI"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Kode Bank</label>
                        <input type="text" name="kode_bank" required placeholder="Contoh: BRI"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="flex items-end gap-3">
                        <label
                            class="flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                            <input type="checkbox" name="aktif" value="1" checked
                                class="h-4 w-4 rounded border-slate-300 text-blue-500">
                            Aktif
                        </label>
                        <button type="submit"
                            class="rounded-xl bg-blue-600 px-4 py-3 font-medium text-white hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Nama Bank</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Kode</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($banks as $bank)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700">{{ $bank->nama_bank }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $bank->kode_bank }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="rounded-full px-2.5 py-1 text-xs font-medium {{ $bank->aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $bank->aktif ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-slate-500">Belum ada data bank.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- <section id="progres" class="rounded-2xl bg-white p-6 shadow-sm">
                <div class="mb-6 flex flex-col gap-1">
                    <h2 class="text-xl font-semibold text-slate-800">Progres</h2>
                    <p class="text-slate-500">Kelola daftar status progres pekerjaan</p>
                </div>

                <form action="{{ route('admin.settings.progresses.store') }}" method="POST"
                    class="mb-6 grid gap-4 md:grid-cols-3">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Nama Progres</label>
                        <input type="text" name="nama" required placeholder="Contoh: Menunggu Konfirmasi"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Kode</label>
                        <input type="text" name="kode" placeholder="Contoh: WAIT"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="flex items-end gap-3">
                        <label
                            class="flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                            <input type="checkbox" name="aktif" value="1" checked
                                class="h-4 w-4 rounded border-slate-300 text-blue-500">
                            Aktif
                        </label>
                        <button type="submit"
                            class="rounded-xl bg-blue-600 px-4 py-3 font-medium text-white hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Nama Progres</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Kode</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($progresses as $item)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700">{{ $item->nama }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $item->kode ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="rounded-full px-2.5 py-1 text-xs font-medium {{ $item->aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $item->aktif ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-slate-500">Belum ada data
                                        progres. Tambahkan melalui form di atas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section> --}}

            <section id="packing" class="rounded-2xl bg-white p-6 shadow-sm">
                <div class="mb-6 flex flex-col gap-1">
                    <h2 class="text-xl font-semibold text-slate-800">Packing</h2>
                    <p class="text-slate-500">Kelola jenis atau status packing</p>
                </div>

                <form action="{{ route('admin.settings.packings.store') }}" method="POST"
                    class="mb-6 grid gap-4 md:grid-cols-3">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Nama Packing</label>
                        <input type="text" name="nama" required placeholder="Contoh: Sudah Dikemas"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Kode</label>
                        <input type="text" name="kode" placeholder="Contoh: PACK"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="flex items-end gap-3">
                        <label
                            class="flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                            <input type="checkbox" name="aktif" value="1" checked
                                class="h-4 w-4 rounded border-slate-300 text-blue-500">
                            Aktif
                        </label>
                        <button type="submit"
                            class="rounded-xl bg-blue-600 px-4 py-3 font-medium text-white hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Nama Packing</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Kode</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
                                {{-- <th class="px-4 py-3 text-left font-semibold text-slate-700">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($packings as $item)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700">{{ $item->nama }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $item->kode ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="rounded-full px-2.5 py-1 text-xs font-medium {{ $item->aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $item->aktif ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    {{-- <td class="px-4 py-3">
                                        <button type="button"
                                            class="rounded-full bg-red-200 px-3 py-1 text-sm font-medium text-red-700 hover:bg-red-300" >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-slate-500">Belum ada data
                                        packing.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="ekspedisi" class="rounded-2xl bg-white p-6 shadow-sm">
                <div class="mb-6 flex flex-col gap-1">
                    <h2 class="text-xl font-semibold text-slate-800">Ekspedisi</h2>
                    <p class="text-slate-500">Kelola daftar ekspedisi yang tersedia</p>
                </div>

                <form action="{{ route('admin.settings.ekspedisis.store') }}" method="POST"
                    class="mb-6 grid gap-4 md:grid-cols-3">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Nama Ekspedisi</label>
                        <input type="text" name="nama" required placeholder="Contoh: JNE"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Kode</label>
                        <input type="text" name="kode" placeholder="Contoh: JNE"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="flex items-end gap-3">
                        <label
                            class="flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                            <input type="checkbox" name="aktif" value="1" checked
                                class="h-4 w-4 rounded border-slate-300 text-blue-500">
                            Aktif
                        </label>
                        <button type="submit"
                            class="rounded-xl bg-blue-600 px-4 py-3 font-medium text-white hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Nama Ekspedisi</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Kode</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($ekspedisis as $item)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700">{{ $item->nama }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $item->kode ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="rounded-full px-2.5 py-1 text-xs font-medium {{ $item->aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $item->aktif ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-slate-500">Belum ada data
                                        ekspedisi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
