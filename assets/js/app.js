document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("penyewaan-container");
    const formTransaksi = document.getElementById("formTransaksi");

    // Fungsi Render Data
    function renderData(data) {
        let html = "";
        data.forEach(trx => {
            let badge = trx.status === "Sedang Disewa" ? "bg-warning text-dark" : "bg-success";
            let tglKembali = trx.tanggal_kembali ? trx.tanggal_kembali : "Belum ditentukan"; 
            
            html += `
                <div class="col-md-6 mb-4">
                    <div class="card custom-card p-3 h-100">
                        <div class="card-body">
                            <h5 class="fw-bold" style="color: #d16565;">${trx.nama_penyewa} <span class="fs-6 text-muted">(${trx.id_transaksi})</span></h5>
                            <div class="bg-light p-2 rounded mb-3 mt-3">
                                <strong>Alat Disewa:</strong> ${trx.alat_disewa} <br>
                                <strong>Durasi:</strong> ${trx.durasi} <br>
                                <strong>Tgl Kembali:</strong> <span class="text-danger">${tglKembali}</span>
                            </div>
                            <span class="badge ${badge} mb-2">${trx.status}</span><br>
                        </div>
                    </div>
                </div>`;
        });
        container.innerHTML = html;
    }

    // Load Data dari LocalStorage atau JSON
    let dataPenyewaan = JSON.parse(localStorage.getItem("dataPenyewaan"));
    if (!dataPenyewaan) {
        fetch('data/penyewaan.json').then(res => res.json()).then(data => {
            localStorage.setItem("dataPenyewaan", JSON.stringify(data));
            renderData(data);
        });
    } else {
        renderData(dataPenyewaan);
    }

    // Event Submit Form Tambah
    formTransaksi.addEventListener("submit", function(e) {
        e.preventDefault();
        let data = JSON.parse(localStorage.getItem("dataPenyewaan")) || [];
        
        let newTrx = {
            id_transaksi: "TRX-" + String(data.length + 1).padStart(3, '0'),
            nama_penyewa: document.getElementById("inputNamaPenyewa").value,
            alat_disewa: document.getElementById("inputAlat").value,
            durasi: document.getElementById("inputDurasi").value,
            tanggal_kembali: document.getElementById("inputTanggal").value,
            status: document.getElementById("inputStatus").value
        };

        data.push(newTrx);
        localStorage.setItem("dataPenyewaan", JSON.stringify(data));
        renderData(data);
        
        // Tutup modal dan reset form
        bootstrap.Modal.getInstance(document.getElementById('modalTransaksi')).hide();
        formTransaksi.reset();
    });
});