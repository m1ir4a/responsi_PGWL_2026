@extends('layouts.template')

@section('styles')

<style>
    .modal-content {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 12px 35px rgba(0,0,0,0.25);
}

/* HEADER */
.modal-header {
    background: linear-gradient(135deg, #2E7D32, #43A047);
    color: white;
    border-bottom: none;
    padding: 16px 20px;
}

.modal-title {
    font-weight: 700;
    font-size: 16px;
}

/* BODY */
.modal-body {
    background: #f7faf7;
    padding: 20px;
}

/* LABEL */
.modal-body .form-label {
    font-weight: 600;
    font-size: 13px;
    color: #2E7D32;
    margin-top: 6px;
}

/* INPUT */
.modal-body .form-control {
    border-radius: 10px;
    border: 1px solid #d8e6da;
    padding: 8px 10px;
    font-size: 14px;
}

.modal-body .form-control:focus {
    border-color: #43A047;
    box-shadow: 0 0 0 0.2rem rgba(67,160,71,.15);
}

/* SECTION TITLE (PADI / JAGUNG / KEDELAI) */
.modal-body h5 {
    margin-top: 18px;
    margin-bottom: 10px;
    font-weight: 700;
    color: #2E7D32;
    border-left: 4px solid #43A047;
    padding-left: 8px;
}

/* FOOTER */
.modal-footer {
    border-top: none;
    background: #fff;
    padding: 15px 20px;
}

/* BUTTON SAVE */
.btn-success {
    background: #2E7D32;
    border: none;
    border-radius: 10px;
    padding: 8px 16px;
}

.btn-success:hover {
    background: #256428;
}

/* BUTTON CANCEL */
.btn-secondary {
    border-radius: 10px;
}

/* MODAL ANIMATION */
.modal.fade .modal-dialog {
    transform: translateY(-20px);
    transition: all 0.25s ease;
}

.modal.show .modal-dialog {
    transform: translateY(0);
}

/* 🔥 UKURAN MODAL LEBIH KECIL */
.modal-dialog {
    max-width: 520px;   /* sebelumnya lg = terlalu besar */
}

/* 🔥 SUPAYA TIDAK KETUTUP NAVBAR */
.modal {
    z-index: 2000 !important;
}

.modal-backdrop {
    z-index: 1990 !important;
}

/* kalau navbar kamu fixed-top Bootstrap */
.navbar {
    z-index: 3000;
    position: relative;
}

/* 🔥 BIAR MODAL TIDAK KEPANJANG */
.modal-body {
    max-height: 65vh;
    overflow-y: auto;
}

/* 🔥 padding lebih ringkas */
.modal-body {
    padding: 15px;
}

/* input lebih compact */
.modal-body .form-control {
    padding: 6px 10px;
    font-size: 13px;
}
</style>

@endsection

@section('content')

<div id="map" style="height:100vh;"></div>

<!-- MODAL CREATE -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('pertanian.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">
                        Tambah Data Pertanian - {{ $kecamatan }}
                    </h5>
                </div>

                <div class="modal-body">

                    <!-- kecamatan otomatis -->
                    <input type="hidden"
                           name="kecamatan"
                           value="{{ $kecamatan }}">

                    <label class="form-label">Tahun</label>
                    <input type="number"
                           name="tahun"
                           class="form-control mb-3"
                           placeholder="Contoh: 2025"
                           required>

                    <!-- PADI -->
                    <h5 class="mb-2">🌾 Padi</h5>

                    <label class="form-label">Luas Panen (Ha)</label>
                    <input name="padi_luas_panen"
                           class="form-control mb-2">

                    <label class="form-label">Produksi (Ton)</label>
                    <input name="padi_produksi"
                           class="form-control mb-2">

                    <label class="form-label">Produktivitas (Ton/Ha)</label>
                    <input name="padi_produktivitas"
                           class="form-control mb-3">

                    <!-- JAGUNG -->
                    <h5 class="mb-2">🌽 Jagung</h5>

                    <label class="form-label">Luas Panen (Ha)</label>
                    <input name="jagung_luas_panen"
                           class="form-control mb-2">

                    <label class="form-label">Produksi (Ton)</label>
                    <input name="jagung_produksi"
                           class="form-control mb-2">

                    <label class="form-label">Produktivitas (Ton/Ha)</label>
                    <input name="jagung_produktivitas"
                           class="form-control mb-3">

                    <!-- KEDELAI -->
                    <h5 class="mb-2">🫘 Kedelai</h5>

                    <label class="form-label">Luas Panen (Ha)</label>
                    <input name="kedelai_luas_panen"
                           class="form-control mb-2">

                    <label class="form-label">Produksi (Ton)</label>
                    <input name="kedelai_produksi"
                           class="form-control mb-2">

                    <label class="form-label">Produktivitas (Ton/Ha)</label>
                    <input name="kedelai_produktivitas"
                           class="form-control mb-2">

                </div>

                <div class="modal-footer">

                    <a href="{{ route('peta') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button class="btn btn-success">
                        Save Data
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection


@push('scripts')

<!-- Leaflet -->
<link rel="stylesheet"
href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

/* MAP */
var map = L.map('map').setView([-7.1,110.9],10);

L.tileLayer(
'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
{
    maxZoom:19
}
).addTo(map);

/* LOAD GEOJSON */
fetch('/geojson-kecamatan')
.then(res => res.json())
.then(data => {

    let selectedName = "{{ $kecamatan }}";

    let geo = L.geoJSON(data, {

        style: function(feature){

            let aktif =
                feature.properties.kecamatan === selectedName;

            return {
                color: aktif ? "yellow" : "#999",
                weight: aktif ? 5 : 1,
                fillColor: aktif ? "#ffeb3b" : "#4CAF50",
                fillOpacity: aktif ? 0.7 : 0.3
            };
        }

    }).addTo(map);

    map.fitBounds(geo.getBounds());

});

/* MODAL OTOMATIS MUNCUL */
document.addEventListener("DOMContentLoaded", function(){

    let modal = new bootstrap.Modal(
        document.getElementById('createModal')
    );

    modal.show();

});

</script>

@endpush
