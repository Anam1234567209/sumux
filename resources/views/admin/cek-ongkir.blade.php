@extends('layouts.admin')

@section('content')
@section('title', 'Cek Ongkir')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Cek Ongkir
            </h1>

            <p class="text-slate-500">
                Hitung estimasi pengiriman SUMUX ke seluruh Indonesia
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="button" onclick="resetForm()"
                class="px-5 py-3 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 transition">
                <i class="fa-solid fa-rotate-right mr-2"></i>
                Reset
            </button>

            <button type="button" onclick="swapDestinations()"
                class="px-5 py-3 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition">
                <i class="fa-solid fa-right-left mr-2"></i>
                Tukar
            </button>
        </div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-slate-400">
                        Cakupan
                    </p>
                    <h2 class="text-2xl font-bold text-slate-800 mt-2">
                        Nasional
                    </h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-slate-400">
                        Data Wilayah
                    </p>
                    <h2 class="text-2xl font-bold text-slate-800 mt-2">
                        Live API
                    </h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-database"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-slate-400">
                        Kurir
                    </p>
                    <h2 class="text-2xl font-bold text-slate-800 mt-2">
                        10 Opsi
                    </h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-6">

        {{-- Form --}}
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">
                            Form Pengiriman
                        </h2>
                        <p class="text-slate-500">
                            Cari kelurahan, kecamatan, kota, atau kode pos
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label for="originSearch" class="block text-sm font-medium text-slate-700 mb-2">
                            Asal Pengiriman
                        </label>
                        <div class="relative">
                            <input id="originSearch" type="text" autocomplete="off"
                                placeholder="Contoh: Rembang, Bandung, 59219"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 pr-11 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                oninput="searchDestinations('origin')">
                            <i class="fa-solid fa-magnifying-glass absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <div id="originResults"
                                class="hidden absolute z-20 mt-2 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                            </div>
                        </div>
                        <input id="origin" type="hidden">
                    </div>

                    <div>
                        <label for="destinationSearch" class="block text-sm font-medium text-slate-700 mb-2">
                            Tujuan Pengiriman
                        </label>
                        <div class="relative">
                            <input id="destinationSearch" type="text" autocomplete="off"
                                placeholder="Contoh: Jakarta Selatan, Surabaya, Denpasar"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 pr-11 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                oninput="searchDestinations('destination')">
                            <i class="fa-solid fa-location-dot absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <div id="destinationResults"
                                class="hidden absolute z-20 mt-2 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                            </div>
                        </div>
                        <input id="destination" type="hidden">
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="weight" class="block text-sm font-medium text-slate-700 mb-2">
                                Berat
                            </label>
                            <div class="relative">
                                <input id="weight" type="number" min="1" value="1000"
                                    class="w-full border border-slate-300 rounded-xl px-4 py-3 pr-16 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">
                                    gram
                                </span>
                            </div>
                        </div>

                        <div>
                            <label for="courier" class="block text-sm font-medium text-slate-700 mb-2">
                                Kurir
                            </label>
                            <select id="courier"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                <option value="jne">JNE</option>
                                <option value="jnt">J&T</option>
                                <option value="sicepat">SiCepat</option>
                                <option value="anteraja">AnterAja</option>
                                <option value="tiki">TIKI</option>
                                <option value="pos">POS Indonesia</option>
                                <option value="ninja">Ninja Xpress</option>
                                <option value="wahana">Wahana</option>
                                <option value="lion">Lion Parcel</option>
                                <option value="sap">SAP Express</option>
                            </select>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-circle-info mt-1 text-blue-500"></i>
                            <p class="text-sm text-slate-600">
                                Untuk hasil paling akurat, pilih wilayah sampai kelurahan atau kode pos. Daftar wilayah diambil langsung dari RajaOngkir/Komerce.
                            </p>
                        </div>
                    </div>

                    <button id="cekOngkirButton" type="button" onclick="cekOngkir()"
                        class="w-full bg-blue-400 text-white rounded-xl py-3 hover:bg-blue-700 transition font-medium">
                        <i class="fa-solid fa-calculator mr-2"></i>
                        Cek Ongkir
                    </button>
                </div>
            </div>
        </div>

        {{-- Results --}}
        <div class="lg:col-span-7">
            <div class="bg-white rounded-2xl p-6 shadow-sm min-h-full">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">
                            Hasil Ongkir
                        </h2>
                        <p class="text-slate-500">
                            Estimasi biaya dan durasi pengiriman
                        </p>
                    </div>

                    <div id="routePreview"
                        class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm text-slate-500">
                        <i class="fa-solid fa-route"></i>
                        Pilih rute dahulu
                    </div>
                </div>

                <div id="status"></div>

                <div id="emptyState" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                    <div class="mx-auto w-14 h-14 rounded-2xl bg-white text-slate-500 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-truck-ramp-box text-xl"></i>
                    </div>
                    <h3 class="mt-4 font-semibold text-slate-800">
                        Belum ada pengecekan
                    </h3>
                    <p class="mt-2 text-slate-500">
                        Lengkapi asal, tujuan, berat, dan kurir untuk menampilkan ongkir.
                    </p>
                </div>

                <div id="hasil" class="mt-5 grid gap-4"></div>
            </div>
        </div>

    </div>
</div>

<script>
    const destinationUrl = '{{ route('api.ongkir.destinations') }}';
    const calculateUrl = '{{ route('api.cek-ongkir') }}';
    const selected = {
        origin: null,
        destination: null,
    };
    const latestResults = {
        origin: [],
        destination: [],
    };
    const searchTimers = {};
    const activeControllers = {};

    const statusEl = document.getElementById('status');
    const hasilEl = document.getElementById('hasil');
    const emptyState = document.getElementById('emptyState');
    const cekButton = document.getElementById('cekOngkirButton');
    const routePreview = document.getElementById('routePreview');

    function searchDestinations(type) {
        const input = document.getElementById(`${type}Search`);
        const resultsEl = document.getElementById(`${type}Results`);
        const query = input.value.trim();

        selected[type] = null;
        document.getElementById(type).value = '';
        updateRoutePreview();

        clearTimeout(searchTimers[type]);

        if (query.length < 2) {
            renderDestinationMessage(resultsEl, 'Ketik minimal 2 karakter.');
            return;
        }

        searchTimers[type] = setTimeout(async () => {
            if (activeControllers[type]) {
                activeControllers[type].abort();
            }

            activeControllers[type] = new AbortController();
            renderDestinationMessage(resultsEl, 'Mencari wilayah...');

            try {
                const response = await fetch(`${destinationUrl}?search=${encodeURIComponent(query)}&limit=25`, {
                    signal: activeControllers[type].signal,
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                const result = await response.json();

                if (!response.ok || !result.ok) {
                    renderDestinationMessage(resultsEl, result.message || 'Gagal mengambil daftar wilayah.');
                    return;
                }

                renderDestinationOptions(type, result.data || []);
            } catch (error) {
                if (error.name !== 'AbortError') {
                    renderDestinationMessage(resultsEl, 'Tidak dapat menghubungi server wilayah.');
                }
            }
        }, 350);
    }

    function renderDestinationOptions(type, items) {
        const resultsEl = document.getElementById(`${type}Results`);
        latestResults[type] = items;

        if (!items.length) {
            renderDestinationMessage(resultsEl, 'Wilayah tidak ditemukan.');
            return;
        }

        resultsEl.classList.remove('hidden');
        resultsEl.innerHTML = items.map((item, index) => `
            <button type="button" onclick="selectDestinationByIndex('${type}', ${index})"
                class="block w-full border-b border-slate-100 px-4 py-3 text-left hover:bg-blue-50 transition last:border-b-0">
                <span class="block font-medium text-slate-800">${escapeHtml(item.subdistrict_name)} - ${escapeHtml(item.district_name)}</span>
                <span class="block text-sm text-slate-500">${escapeHtml(item.city_name)}, ${escapeHtml(item.province_name)} ${escapeHtml(item.zip_code)}</span>
            </button>
        `).join('');
    }

    function renderDestinationMessage(resultsEl, message) {
        resultsEl.classList.remove('hidden');
        resultsEl.innerHTML = `<div class="px-4 py-3 text-sm text-slate-500">${message}</div>`;
    }

    function selectDestinationByIndex(type, index) {
        const item = latestResults[type][index];

        if (item) {
            selectDestination(type, item);
        }
    }

    function selectDestination(type, item) {
        selected[type] = item;
        document.getElementById(type).value = item.id;
        document.getElementById(`${type}Search`).value = item.label;
        document.getElementById(`${type}Results`).classList.add('hidden');
        updateRoutePreview();
    }

    async function cekOngkir() {
        const origin = document.getElementById('origin').value.trim();
        const destination = document.getElementById('destination').value.trim();
        const weight = document.getElementById('weight').value.trim();
        const courier = document.getElementById('courier').value;

        if (!origin || !destination || !weight || Number(weight) <= 0) {
            showStatus('error', 'Silakan pilih asal, tujuan, dan berat barang dengan benar.');
            hasilEl.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        if (origin === destination) {
            showStatus('error', 'Asal dan tujuan tidak boleh sama.');
            hasilEl.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        showStatus('loading', 'Memproses cek ongkir...');
        hasilEl.innerHTML = '';
        emptyState.classList.add('hidden');
        cekButton.disabled = true;
        cekButton.classList.add('opacity-70', 'cursor-not-allowed');
        cekButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Memeriksa...';

        try {
            const response = await fetch(calculateUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    origin,
                    destination,
                    weight,
                    courier,
                }),
            });

            const result = await response.json();

            if (!response.ok || !result.ok) {
                showStatus('error', result.message || 'Gagal memproses permintaan ongkir.');
                emptyState.classList.remove('hidden');
                return;
            }

            if (!Array.isArray(result.data) || result.data.length === 0) {
                showStatus('warning', 'Tidak ada layanan ongkir untuk kombinasi rute dan kurir ini.');
                emptyState.classList.remove('hidden');
                return;
            }

            showStatus('success', `Ditemukan ${result.data.length} layanan ongkir.`);
            renderCosts(result.data);
        } catch (error) {
            showStatus('error', 'Terjadi kesalahan saat menghubungi server. Silakan coba lagi.');
            emptyState.classList.remove('hidden');
        } finally {
            cekButton.disabled = false;
            cekButton.classList.remove('opacity-70', 'cursor-not-allowed');
            cekButton.innerHTML = '<i class="fa-solid fa-calculator mr-2"></i>Cek Ongkir';
        }
    }

    function renderCosts(items) {
        emptyState.classList.add('hidden');
        hasilEl.innerHTML = items.map((item, index) => `
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-blue-200 transition">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-sm font-semibold text-blue-600">
                                ${index + 1}
                            </span>
                            <h3 class="font-semibold text-slate-800">${escapeHtml(item.name || item.code || '-')}</h3>
                        </div>
                        <p class="mt-2 text-sm text-slate-500">
                            ${escapeHtml(item.service || '-')} - ${escapeHtml(item.description || 'Layanan pengiriman')}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:min-w-64">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs uppercase text-slate-400">Estimasi</p>
                            <p class="mt-1 font-semibold text-slate-800">${escapeHtml(item.etd || '-')}</p>
                        </div>
                        <div class="rounded-xl bg-blue-50 p-4">
                            <p class="text-xs uppercase text-blue-500">Biaya</p>
                            <p class="mt-1 font-bold text-blue-700">Rp ${escapeHtml(String(item.cost ?? '0'))}</p>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function showStatus(type, message) {
        const styles = {
            error: 'text-red-700 bg-red-50 border-red-200',
            warning: 'text-amber-700 bg-amber-50 border-amber-200',
            success: 'text-green-700 bg-green-50 border-green-200',
            loading: 'text-slate-700 bg-slate-50 border-slate-200',
        };

        const icons = {
            error: 'fa-circle-exclamation',
            warning: 'fa-triangle-exclamation',
            success: 'fa-circle-check',
            loading: 'fa-spinner fa-spin',
        };

        statusEl.innerHTML = `
            <div class="mb-5 rounded-xl border p-4 ${styles[type] || styles.loading}">
                <i class="fa-solid ${icons[type] || icons.loading} mr-2"></i>
                ${message}
            </div>
        `;
    }

    function updateRoutePreview() {
        if (!selected.origin || !selected.destination) {
            routePreview.innerHTML = '<i class="fa-solid fa-route"></i> Pilih rute dahulu';
            routePreview.className =
                'inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm text-slate-500';
            return;
        }

        routePreview.innerHTML =
            `<i class="fa-solid fa-route"></i> ${escapeHtml(selected.origin.city_name)} ke ${escapeHtml(selected.destination.city_name)}`;
        routePreview.className =
            'inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm text-blue-700';
    }

    function resetForm() {
        ['origin', 'destination'].forEach(type => {
            selected[type] = null;
            document.getElementById(type).value = '';
            document.getElementById(`${type}Search`).value = '';
            document.getElementById(`${type}Results`).classList.add('hidden');
        });

        document.getElementById('weight').value = 1000;
        document.getElementById('courier').value = 'jne';
        statusEl.innerHTML = '';
        hasilEl.innerHTML = '';
        emptyState.classList.remove('hidden');
        updateRoutePreview();
    }

    function swapDestinations() {
        const origin = selected.origin;
        const destination = selected.destination;
        const originText = document.getElementById('originSearch').value;
        const destinationText = document.getElementById('destinationSearch').value;

        selected.origin = destination;
        selected.destination = origin;
        document.getElementById('origin').value = destination?.id || '';
        document.getElementById('destination').value = origin?.id || '';
        document.getElementById('originSearch').value = destinationText;
        document.getElementById('destinationSearch').value = originText;
        updateRoutePreview();
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    document.addEventListener('click', event => {
        ['origin', 'destination'].forEach(type => {
            const input = document.getElementById(`${type}Search`);
            const results = document.getElementById(`${type}Results`);

            if (!input.contains(event.target) && !results.contains(event.target)) {
                results.classList.add('hidden');
            }
        });
    });
</script>
@endsection
