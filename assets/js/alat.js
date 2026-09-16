document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("alat-container");
    const formAlat = document.getElementById("formAlat");

    function renderData(data) {
        let html = "";
        data.forEach(alat => {
            let stockColor = alat.stok > 0 ? "text-success" : "text-danger";
            html += `
                <div class="col-md-4 mb-4">
                    <div class="card custom-card p-3 h-100 text-center">
                        <div class="card-body">
                            <h5 class="fw-bold" style="color: #d16565;">${alat.nama_alat}</h5>
                            <h6 class="text-muted">Kode: ${alat.kode_alat}</h6>
                            <hr style="border-color: #ffb7b2;">
                            <p class="mb-1"><strong>Merk:</strong> ${alat.merk}</p>
                            <p class="mb-2"><strong>Harga:</strong> <span class="badge bg-info text-dark">${alat.harga_sewa}</span></p>
                            <p class="fw-bold ${stockColor}">Sisa Stok: ${alat.stok} unit</p>
                        </div>
                    </div>
                </div>`;
        });
        container.innerHTML = html;
    }

    let dataAlat = JSON.parse(localStorage.getItem("dataAlat"));
    if (!dataAlat) {
        fetch('../data/alat.json').then(res => res.json()).then(data => {
            localStorage.setItem("dataAlat", JSON.stringify(data));
            renderData(data);
        });
    } else {
        renderData(dataAlat);
    }

    formAlat.addEventListener("submit", function(e) {
        e.preventDefault();
        let data = JSON.parse(localStorage.getItem("dataAlat")) || [];
        
        let newAlat = {
            kode_alat: "ALT-" + String(data.length + 1).padStart(2, '0'),
            nama_alat: document.getElementById("inputNamaAlat").value,
            merk: document.getElementById("inputMerk").value,
            stok: parseInt(document.getElementById("inputStok").value),
            harga_sewa: document.getElementById("inputHarga").value
        };

        data.push(newAlat);
        localStorage.setItem("dataAlat", JSON.stringify(data));
        renderData(data);
        
        bootstrap.Modal.getInstance(document.getElementById('modalAlat')).hide();
        formAlat.reset();
    });
});