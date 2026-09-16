document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("penyewa-container");
    const formPenyewa = document.getElementById("formPenyewa");

    function renderData(data) {
        let html = "";
        data.forEach(penyewa => {
            html += `
                <div class="col-md-4 mb-4">
                    <div class="card custom-card p-3 h-100">
                        <div class="card-body text-center">
                            <h5 class="fw-bold" style="color: #d16565;">${penyewa.nama}</h5>
                            <h6 class="text-muted">ID: ${penyewa.id_penyewa}</h6>
                            <hr style="border-color: #ffb7b2;">
                            <p class="mb-1"><strong>No HP:</strong> ${penyewa.no_hp}</p>
                            <p class="mb-3"><strong>Jaminan:</strong> <span class="badge bg-secondary">${penyewa.jaminan}</span></p>
                        </div>
                    </div>
                </div>`;
        });
        container.innerHTML = html;
    }

    let dataPenyewa = JSON.parse(localStorage.getItem("dataPenyewa"));
    if (!dataPenyewa) {
        fetch('../data/penyewa.json').then(res => res.json()).then(data => {
            localStorage.setItem("dataPenyewa", JSON.stringify(data));
            renderData(data);
        });
    } else {
        renderData(dataPenyewa);
    }

    formPenyewa.addEventListener("submit", function(e) {
        e.preventDefault();
        let data = JSON.parse(localStorage.getItem("dataPenyewa")) || [];
        
        let newPenyewa = {
            id_penyewa: "CUST-" + String(data.length + 1).padStart(3, '0'),
            nama: document.getElementById("inputNama").value,
            no_hp: document.getElementById("inputNoHP").value,
            jaminan: document.getElementById("inputJaminan").value
        };

        data.push(newPenyewa);
        localStorage.setItem("dataPenyewa", JSON.stringify(data));
        renderData(data);
        
        bootstrap.Modal.getInstance(document.getElementById('modalPenyewa')).hide();
        formPenyewa.reset();
    });
});