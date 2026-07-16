document.addEventListener("DOMContentLoaded", function () {
    function parseRupiah(value) {
        return Number(String(value || "").replace(/\D/g, "")) || 0;
    }

    function formatRupiah(value) {
        const number = parseRupiah(value);
        return number ? number.toLocaleString("id-ID") : "";
    }

    function updateSisaTagihan() {
        const totalTagihanInput = document.querySelector('input[name="total_tagihan"]');
        const totalDibayarInput = document.querySelector('input[name="total_dibayar"]');
        const sisaTagihanInput = document.querySelector('input[name="sisa_tagihan"]');
        const statusPembayaranPreview = document.getElementById("statusPembayaranPreview");

        if (!totalTagihanInput || !totalDibayarInput || !sisaTagihanInput) {
            return;
        }

        const totalTagihan = parseRupiah(totalTagihanInput.value);
        const totalDibayar = parseRupiah(totalDibayarInput.value);
        const sisaTagihan = Math.max(totalTagihan - totalDibayar, 0);

        sisaTagihanInput.value = formatRupiah(sisaTagihan);

        if (statusPembayaranPreview) {
            if (sisaTagihan <= 0) {
                statusPembayaranPreview.value = "Lunas";
            } else if (totalTagihan <= 0 || sisaTagihan >= totalTagihan) {
                statusPembayaranPreview.value = "Belum bayar";
            } else {
                statusPembayaranPreview.value = "DP";
            }
        }
    }

    function updateTotalTagihan() {
        const subtotalProduk = Array.from(document.querySelectorAll(".subtotal")).reduce(
            (sum, input) => sum + parseRupiah(input.value),
            0
        );

        const ongkirInput = document.querySelector('input[name="biaya_ongkir"]');
        const ongkir = parseRupiah(ongkirInput?.value);

        const total = subtotalProduk + ongkir;

        const totalTagihanInput = document.querySelector('input[name="total_tagihan"]');
        if (totalTagihanInput) {
            totalTagihanInput.value = total;
        }

        updateSisaTagihan();
    }

    function updateSubtotal(row) {
        const priceInput = row.querySelector(".price");
        const qtyInput = row.querySelector(".qty");
        const subtotalInput = row.querySelector(".subtotal");

        const price = parseRupiah(priceInput?.value);
        const qty = Number(qtyInput?.value || 0);
        const subtotal = price * qty;

        if (priceInput) {
            priceInput.value = formatRupiah(price);
        }

        if (subtotalInput) {
            subtotalInput.value = formatRupiah(subtotal);
        }

        updateTotalTagihan();
    }

    document.addEventListener("input", function (event) {
        if (event.target.name === "total_dibayar") {
            event.target.value = formatRupiah(event.target.value);
            updateSisaTagihan();
            return;
        }

        if (event.target.name === "biaya_ongkir") {
            event.target.value = formatRupiah(event.target.value);
            updateTotalTagihan();
            return;
        }

        if (!event.target.classList.contains("price") && !event.target.classList.contains("qty")) {
            return;
        }

        const row = event.target.closest(".product-row");
        if (row) {
            updateSubtotal(row);
        }
    });

    document.addEventListener("submit", function (event) {
        if (event.target.id !== "orderForm") {
            return;
        }

        updateTotalTagihan();
    });

    window.updateOrderTotals = updateTotalTagihan;

    updateTotalTagihan();
});
