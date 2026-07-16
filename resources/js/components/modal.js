// modal popup
function normalizeAmount(value) {
    if (typeof value === 'number') {
        return value;
    }

    if (typeof value !== 'string') {
        return 0;
    }

    const normalized = value
        .replace(/\./g, '')
        .replace(/,/g, '.')
        .replace(/[^(0-9|\.)]/g, '');

    const parsed = Number(normalized);
    return Number.isFinite(parsed) ? parsed : 0;
}

function formatRupiah(value) {
    const number = normalizeAmount(value);
    return number ? number.toLocaleString('id-ID') : '';
}

function getField(name) {
    return document.querySelector(`[name="${name}"]`);
}

function setFieldValue(name, value) {
    const field = getField(name);
    if (!field) {
        return;
    }

    if (field.type === 'checkbox' || field.type === 'radio') {
        field.checked = Boolean(value);
        return;
    }

    field.value = value ?? '';
}

function setPreviewImage(inputId, previewId, placeholder) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (!input || !preview) {
        return;
    }

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) {
            preview.src = placeholder;
            return;
        }

        preview.src = URL.createObjectURL(file);
        preview.onload = function () {
            URL.revokeObjectURL(preview.src);
        };
    });
}

function resetFormFields() {
    const form = document.getElementById('orderForm');
    form.reset();

    const previewProduk = document.getElementById('previewProduk');
    const previewBukti = document.getElementById('previewBukti');

    if (previewProduk) {
        previewProduk.src = 'https://placehold.co/280x170/e2e8f0/64748b?text=Belum+Ada+Foto';
    }

    if (previewBukti) {
        previewBukti.src = 'https://placehold.co/280x170/e2e8f0/64748b?text=Belum+Ada+Foto';
    }

    const productRows = document.getElementById('productRows');
    if (productRows) {
        productRows.innerHTML = '';
        productRows.appendChild(createProductRow());
    }
}

function createProductRow(item = {}) {
    const row = document.createElement('div');
    row.className = 'product-row grid gap-3.5 sm:grid-cols-2 border border-slate-200 rounded-2xl p-4 relative';
    const priceValue = item.price ? formatRupiah(item.price) : '';
    const subtotalValue = item.subtotal ? formatRupiah(item.subtotal) : '';
    const photoPreview = item.photo_url || 'https://placehold.co/120x120/e2e8f0/64748b?text=Foto';
    row.innerHTML = `
        <button type="button" onclick="removeProductRow(this)"
            class="absolute top-3 right-3 w-7 h-7 rounded-full bg-rose-50 text-rose-500 hover:bg-rose-100 text-xs">
            <i class="fa-solid fa-trash"></i>
        </button>
        <div>
            <input name="item_name[]" placeholder="Nama Produk" required
                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none"
                value="${item.name ?? ''}">
        </div>
        <div>
            <input name="item_price[]" type="text" min="0" step="100"
                placeholder="Harga Produk" required
                class="price w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none"
                value="${priceValue}">
        </div>
        <div>
            <input name="item_qty[]" type="number" min="1" placeholder="Jumlah" required
                class="qty w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none"
                value="${item.qty ?? ''}">
        </div>
        <div>
            <select name="item_unit[]" required
                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                <option value="unt" ${item.unit === 'unt' ? 'selected' : ''}>Unit</option>
                <option value="gram" ${item.unit === 'gram' ? 'selected' : ''}>Set</option>
                <option value="pcs" ${item.unit === 'pcs' ? 'selected' : ''}>Pcs</option>
            </select>
        </div>
        <div class="col-span-2">
            <input name="item_subtotal[]" type="text" min="0" step="100"
                placeholder="Subtotal"
                class="subtotal w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none bg-slate-50"
                readonly value="${subtotalValue}">
        </div>
        <div class="col-span-2 flex items-center gap-3">
            <img class="row-photo-preview w-14 h-14 rounded-xl object-cover ring-1 ring-slate-200" src="${photoPreview}">
            <label class="flex-1 cursor-pointer rounded-xl border border-dashed border-slate-300 px-3 py-2 text-xs text-slate-500 text-center hover:border-blue-400">
                Pilih Foto Produk Ini
                <input type="file" name="item_photo[]" accept="image/*" class="hidden row-photo-input">
            </label>
            <input type="hidden" name="item_existing_photo[]" value="${item.photo_url ?? ''}">
        </div>
    `;
    return row;
}

function addProductRow() {
    document.getElementById('productRows').appendChild(createProductRow());
}

function removeProductRow(buttonEl) {
    const rows = document.querySelectorAll('#productRows .product-row');
    if (rows.length <= 1) {
        alert('Minimal harus ada 1 produk.');
        return;
    }
    buttonEl.closest('.product-row').remove();
}

document.addEventListener('change', function (event) {
    if (!event.target.classList.contains('row-photo-input')) {
        return;
    }
    const file = event.target.files[0];
    const preview = event.target.closest('.product-row').querySelector('.row-photo-preview');
    if (file && preview) {
        preview.src = URL.createObjectURL(file);
    }
});

window.addProductRow = addProductRow;
window.removeProductRow = removeProductRow;

function fillOrderForm(order) {
    setFieldValue('customer_name', order.customer_name);
    setFieldValue('nomor_pesanan', order.nomor_pesanan);
    setFieldValue('nomor_whatsapp', order.nomor_whatsapp);
    setFieldValue('customer_email', order.customer_email);
    setFieldValue('alamat_pengiriman', order.alamat_pengiriman);
    setFieldValue('tanggal_pesanan', order.tanggal_pesanan);
    setFieldValue('status_pesanan', order.status_pesanan);
    setFieldValue('status_packing', order.status_packing);
    setFieldValue('total_tagihan', order.total_tagihan ? formatRupiah(order.total_tagihan) : '');
    setFieldValue('total_dibayar', order.total_dibayar ? formatRupiah(order.total_dibayar) : '');
    setFieldValue('sisa_tagihan', order.sisa_tagihan ? formatRupiah(order.sisa_tagihan) : '');
    setFieldValue('biaya_ongkir', order.biaya_ongkir ? formatRupiah(order.biaya_ongkir) : '0');
    setFieldValue('metode_pembayaran', order.metode_pembayaran_id ?? '');
    setFieldValue('kurir_id', order.kurir_id);

    const productRows = document.getElementById('productRows');
    if (productRows) {
        productRows.innerHTML = '';
        const items = Array.isArray(order.items) && order.items.length ? order.items : [{}];
        items.forEach((item) => productRows.appendChild(createProductRow(item)));
    }
}

function openOrderModal(orderId = null) {
    const modal = document.getElementById('orderModal');
    const form = document.getElementById('orderForm');
    const title = document.getElementById('orderModalTitle');
    const methodInput = document.getElementById('orderMethod');
    const idInput = document.getElementById('orderId');

    if (orderId) {
        title.textContent = 'Edit Pesanan';
        methodInput.value = 'PUT';
        idInput.value = orderId;
        form.action = `/admin/pesanan/${orderId}`;

        fetch(`/admin/pesanan/${orderId}/edit`, {
            headers: {
                Accept: 'application/json',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Gagal memuat data pesanan');
                }
                return response.json();
            })
            .then((data) => {
                fillOrderForm(data);
                document.body.classList.add('overflow-hidden');
                modal.classList.remove('hidden');
            })
            .catch((error) => {
                console.error(error);
                alert('Tidak dapat memuat data pesanan untuk edit. Muat ulang halaman dan coba lagi.');
            });
    } else {
        title.textContent = 'Tambah Pesanan';
        methodInput.value = 'POST';
        idInput.value = '';
        form.action = '/admin/pesanan';
        resetFormFields();
        document.body.classList.add('overflow-hidden');
        modal.classList.remove('hidden');
    }
}

function closeOrderModal() {
    const modal = document.getElementById('orderModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

window.openOrderModal = openOrderModal;
window.closeOrderModal = closeOrderModal;

function bindQuickEditSelects() {
    document.querySelectorAll('.quick-edit-select').forEach((select) => {
        select.dataset.previousValue = select.value;
    });
}

function bindModalTriggers() {
    document.querySelector('[data-open-order-modal]')?.addEventListener('click', () => {
        openOrderModal();
    });
}

function bindFilePreviews() {
    setPreviewImage('fotoProduk', 'previewProduk', 'https://placehold.co/280x170/e2e8f0/64748b?text=Belum+Ada+Foto');
    setPreviewImage('buktiTransaksi', 'previewBukti', 'https://placehold.co/280x170/e2e8f0/64748b?text=Belum+Ada+Foto');
}

function bindAlerts() {
    const successAlert = document.getElementById('success-alert');
    const errorAlert = document.getElementById('error-alert');

    if (successAlert) {
        setTimeout(() => {
            successAlert.classList.add('opacity-0');
            setTimeout(() => {
                successAlert.remove();
            }, 500);
        }, 3000);
    }

    if (errorAlert) {
        setTimeout(() => {
            errorAlert.classList.add('opacity-0');
            setTimeout(() => {
                errorAlert.remove();
            }, 500);
        }, 3000);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    bindModalTriggers();
    bindFilePreviews();
    bindAlerts();
    bindQuickEditSelects();
});

const QUICK_EDIT_COLOR_MAP = {
    status_pesanan: {
        pending: 'bg-yellow-50 text-yellow-700',
        produksi: 'bg-blue-50 text-blue-600',
        finishing: 'bg-indigo-50 text-indigo-600',
        selesai: 'bg-emerald-50 text-emerald-600',
        dikirim: 'bg-cyan-50 text-cyan-600',
        dibatalkan: 'bg-rose-50 text-rose-600',
    },
    status_packing: {
        belum: 'bg-orange-50 text-orange-600',
        proses: 'bg-blue-50 text-blue-600',
        selesai: 'bg-emerald-50 text-emerald-600',
    },
};

function applyQuickEditColor(selectEl, field, value) {
    const colorMap = QUICK_EDIT_COLOR_MAP[field];
    if (!colorMap) {
        return;
    }

    const newClasses = colorMap[value] ?? 'bg-slate-50 text-slate-600';

    Object.values(colorMap).forEach((classString) => {
        classString.split(' ').forEach((cls) => selectEl.classList.remove(cls));
    });
    selectEl.classList.remove('bg-slate-50', 'text-slate-600');

    newClasses.split(' ').forEach((cls) => selectEl.classList.add(cls));
}

function quickUpdateOrder(selectEl) {
    const orderId = selectEl.dataset.orderId;
    const field = selectEl.dataset.field;
    const value = selectEl.value;
    const previousValue = selectEl.dataset.previousValue ?? '';
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    selectEl.disabled = true;

    fetch(`/admin/pesanan/${orderId}/quick-update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({ field, value }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Gagal memperbarui data');
            }
            return response.json();
        })
        .then(() => {
            selectEl.dataset.previousValue = value;
            applyQuickEditColor(selectEl, field, value);
        })
        .catch((error) => {
            console.error(error);
            selectEl.value = previousValue;
            alert('Gagal menyimpan perubahan. Coba lagi.');
        })
        .finally(() => {
            selectEl.disabled = false;
        });
}

window.quickUpdateOrder = quickUpdateOrder;

function openQuickPay(buttonEl, orderId, bankId) {
    const popover = document.getElementById('quickPayPopover');
    const bankSelect = document.getElementById('quickPayBank');

    document.getElementById('quickPayOrderId').value = orderId;
    document.getElementById('quickPayAmount').value = '';

    if (bankId) {
        bankSelect.value = bankId;
    }

    popover.classList.remove('hidden');
    popover.classList.add('flex');

    document.getElementById('quickPayAmount').focus();
}

function closeQuickPay() {
    const popover = document.getElementById('quickPayPopover');
    popover.classList.add('hidden');
    popover.classList.remove('flex');
}

function submitQuickPay() {
    const orderId = document.getElementById('quickPayOrderId').value;
    const amountInput = document.getElementById('quickPayAmount');
    const bankId = document.getElementById('quickPayBank').value;
    const buktiFile = document.getElementById('quickPayBukti').files[0];
    const jumlahBayar = normalizeAmount(amountInput.value);
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!jumlahBayar || jumlahBayar <= 0) {
        alert('Isi jumlah bayar dengan benar.');
        return;
    }

    const formData = new FormData();
    formData.append('jumlah_bayar', jumlahBayar);
    formData.append('bank_id', bankId);
    if (buktiFile) {
        formData.append('bukti_pembayaran', buktiFile);
    }

    fetch(`/admin/pesanan/${orderId}/payments`, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: formData,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Gagal menyimpan pembayaran');
            }
            return response.json();
        })
        .then(() => {
            window.location.reload();
        })
        .catch((error) => {
            console.error(error);
            alert('Gagal menyimpan pembayaran. Coba lagi.');
        });
}

document.addEventListener('input', function (event) {
    if (event.target.id === 'quickPayAmount') {
        event.target.value = formatRupiah(event.target.value);
    }
});

window.openQuickPay = openQuickPay;
document.getElementById('quickPayPopover')?.addEventListener('click', function (event) {
    if (event.target === this) {
        closeQuickPay();
    }
});
window.closeQuickPay = closeQuickPay;
window.submitQuickPay = submitQuickPay;

    