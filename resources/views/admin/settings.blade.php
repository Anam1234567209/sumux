@extends('layouts.admin')

@section('content')
@section('title', 'Pengaturan')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Pengaturan
            </h1>

            <p class="text-slate-500">
                Kelola profil, website, integrasi ongkir, dan keamanan akun admin
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button class="px-5 py-3 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 transition">
                <i class="fa-solid fa-rotate-right mr-2"></i>
                Reset
            </button>

            <button class="px-5 py-3 rounded-xl bg-blue-400 text-white hover:bg-blue-700 transition">
                <i class="fa-solid fa-floppy-disk mr-2"></i>
                Simpan Perubahan
            </button>
        </div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-slate-400">
                        Status Website
                    </p>

                    <h2 class="text-2xl font-bold text-slate-800 mt-2">
                        Aktif
                    </h2>
                </div>

                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-slate-400">
                        API Ongkir
                    </p>

                    <h2 class="text-2xl font-bold text-slate-800 mt-2">
                        Terhubung
                    </h2>
                </div>

                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-slate-400">
                        Total Admin
                    </p>

                    <h2 class="text-2xl font-bold text-slate-800 mt-2">
                        2
                    </h2>
                </div>

                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-6">

        {{-- Navigation --}}
        <aside class="lg:col-span-4 xl:col-span-3">
            <div class="bg-white rounded-2xl p-4 shadow-sm">
                <nav class="space-y-2">
                    <a href="#profil"
                        class="flex items-center justify-between gap-3 p-3 rounded-xl bg-blue-50 text-blue-700 font-medium">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-user-gear w-5"></i>
                            Kelola Admin
                        </span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>

                    <a href="#website"
                        class="flex items-center gap-3 p-3 rounded-xl text-slate-600 hover:bg-slate-50 transition">
                        <i class="fa-solid fa-globe w-5"></i>
                        Website
                    </a>

                    <a href="#ongkir"
                        class="flex items-center gap-3 p-3 rounded-xl text-slate-600 hover:bg-slate-50 transition">
                        <i class="fa-solid fa-truck w-5"></i>
                        API Ongkir
                    </a>

                    <a href="#notifikasi"
                        class="flex items-center gap-3 p-3 rounded-xl text-slate-600 hover:bg-slate-50 transition">
                        <i class="fa-regular fa-bell w-5"></i>
                        Notifikasi
                    </a>

                    <a href="#keamanan"
                        class="flex items-center gap-3 p-3 rounded-xl text-slate-600 hover:bg-slate-50 transition">
                        <i class="fa-solid fa-lock w-5"></i>
                        Keamanan Sistem
                    </a>
                </nav>
            </div>
        </aside>

        {{-- Content --}}
        <div class="lg:col-span-8 xl:col-span-9 space-y-6">

            <section id="profil" class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col gap-1 mb-6">
                    <h2 class="text-xl font-semibold text-slate-800">
                        Profil Akun
                    </h2>

                    <p class="text-slate-500">
                        Informasi admin yang tampil di panel pengelolaan SUMUX
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Nama Lengkap
                        </label>
                        <input type="text" value="Admin"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Email
                        </label>
                        <input type="email" value="admin@sumux.id"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Nomor HP
                        </label>
                        <input type="text" placeholder="08xxxxxxxxxx"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Role
                        </label>
                        <select
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option>Administrator</option>
                            <option>Staff Operasional</option>
                            <option>Keuangan</option>
                        </select>
                    </div>
                </div>
            </section>

            <section id="website" class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col gap-1 mb-6">
                    <h2 class="text-xl font-semibold text-slate-800">
                        Website
                    </h2>

                    <p class="text-slate-500">
                        Atur identitas dan kontak utama bisnis
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Nama Website
                        </label>
                        <input type="text" value="SUMUX Property & Interior"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            WhatsApp Utama
                        </label>
                        <input type="text" placeholder="+62 812 0000 0000"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Alamat Kantor
                        </label>
                        <textarea rows="3" placeholder="Masukkan alamat lengkap"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"></textarea>
                    </div>
                </div>
            </section>

            <section id="ongkir" class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col gap-1 mb-6">
                    <h2 class="text-xl font-semibold text-slate-800">
                        API Ongkir
                    </h2>

                    <p class="text-slate-500">
                        Konfigurasi layanan pengiriman untuk halaman cek ongkir
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            API Key
                        </label>
                        <div class="flex gap-3">
                            <input type="password" value="sumux-api-key"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <button class="px-4 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Kurir Default
                        </label>
                        <select
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option>JNE</option>
                            <option>J&T</option>
                            <option>SiCepat</option>
                            <option>AnterAja</option>
                        </select>
                    </div>
                </div>

                <div
                    class="mt-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4">
                    <div>
                        <p class="font-semibold text-emerald-700">
                            Koneksi API aktif
                        </p>
                        <p class="text-sm text-emerald-700/80">
                            Sinkronisasi terakhir: 28 Juni 2026, 09:30
                        </p>
                    </div>

                    <button class="px-5 py-3 rounded-xl bg-white text-emerald-700 hover:bg-emerald-100 transition">
                        <i class="fa-solid fa-plug-circle-check mr-2"></i>
                        Tes Koneksi
                    </button>
                </div>
            </section>

            <section id="notifikasi" class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col gap-1 mb-6">
                    <h2 class="text-xl font-semibold text-slate-800">
                        Notifikasi
                    </h2>

                    <p class="text-slate-500">
                        Pilih aktivitas yang perlu dikirim ke admin
                    </p>
                </div>

                <div class="space-y-4">
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-4">
                        <span>
                            <span class="block font-medium text-slate-700">Pesanan baru</span>
                            <span class="block text-sm text-slate-500">Kirim pemberitahuan saat customer masuk</span>
                        </span>
                        <input type="checkbox" checked class="w-5 h-5 rounded border-slate-300 text-blue-500">
                    </label>

                    <label class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-4">
                        <span>
                            <span class="block font-medium text-slate-700">Pembayaran masuk</span>
                            <span class="block text-sm text-slate-500">Pantau dokumen dan status pembayaran</span>
                        </span>
                        <input type="checkbox" checked class="w-5 h-5 rounded border-slate-300 text-blue-500">
                    </label>
                </div>
            </section>

            <section id="keamanan" class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col gap-1 mb-6">
                    <h2 class="text-xl font-semibold text-slate-800">
                        Keamanan
                    </h2>

                    <p class="text-slate-500">
                        Perbarui password secara berkala untuk menjaga akses admin
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Password Baru
                        </label>
                        <input type="password" placeholder="Masukkan password baru"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Konfirmasi Password
                        </label>
                        <input type="password" placeholder="Ulangi password baru"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
            </section>

        </div>

    </div>

</div>
@endsection
