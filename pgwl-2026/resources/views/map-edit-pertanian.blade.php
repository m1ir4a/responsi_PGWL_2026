@extends('layouts.template')

@section('styles')

<style>

html,body{
    height:100%;
    margin:0;
    padding:0;
}

#map{
    height:calc(100vh - 56px);
    width:100%;
}

/* ==========================
   MODAL
========================== */

#editModal .modal-dialog{
    max-width:900px;
}

#editModal .modal-content{
    border:none;
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,.25);
    animation:zoomIn .25s ease;
}

@keyframes zoomIn{
    from{
        opacity:0;
        transform:scale(.92);
    }
    to{
        opacity:1;
        transform:scale(1);
    }
}

#editModal .modal-header{
    background:linear-gradient(135deg,#1B5E20,#43A047);
    color:white;
    border:none;
    padding:18px 24px;
}

#editModal .modal-title{
    font-weight:700;
}

#editModal .modal-body{
    background:#f7f9f8;
    padding:25px;
}

#editModal .modal-footer{
    border:none;
    padding:18px 24px;
}

/* ==========================
   CARD KOMODITAS
========================== */

.crop-card{
    background:white;
    border-radius:16px;
    padding:18px;
    margin-bottom:20px;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
    border-left:5px solid #43A047;
}

.crop-title{
    font-size:18px;
    font-weight:700;
    color:#2E7D32;
    margin-bottom:15px;
}

/* ==========================
   INPUT
========================== */

.form-label{
    font-weight:600;
    color:#555;
    margin-bottom:5px;
}

.form-control{
    border-radius:10px;
    border:1px solid #dcdcdc;
    padding:10px 12px;
}

.form-control:focus{
    border-color:#43A047;
    box-shadow:0 0 0 .15rem rgba(67,160,71,.15);
}

/* ==========================
   BUTTON
========================== */

.btn-save{
    background:#2E7D32;
    color:white;
    border:none;
    border-radius:10px;
    padding:10px 22px;
    font-weight:600;
}

.btn-save:hover{
    background:#256428;
}

.btn-cancel{
    border-radius:10px;
    padding:10px 20px;
}

/* ==========================
   INFO BOX
========================== */

.map-info{
    position:absolute;
    top:80px;
    left:15px;
    z-index:999;
    background:white;
    padding:12px 16px;
    border-radius:14px;
    box-shadow:0 4px 15px rgba(0,0,0,.15);
}

.map-info h5{
    margin:0;
    color:#2E7D32;
    font-weight:700;
}

.map-info small{
    color:#777;
}

.modal-backdrop.show{
    opacity:.4;
    backdrop-filter:blur(4px);
}

#editModal{
    z-index: 9999 !important;
}

#editModal .modal-dialog{
    max-width: 650px; /* lebih kecil */
    margin-top: 80px; /* tidak ketutupan navbar */
}

#editModal .modal-content{
    border:none;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.18);
}

#editModal .modal-body{
    max-height:65vh;
    overflow-y:auto;
    padding:20px;
}

.section-card{
    padding:14px;
    margin-bottom:12px;
    border-radius:12px;
}

.section-title{
    font-size:15px;
    margin-bottom:10px;
}

.form-control{
    height:38px;
    font-size:14px;
}

</style>

@endsection

@section('content')

<div id="map" style="height:100vh;"></div>

<!-- MODAL EDIT -->
<div class="modal fade" id="editModal">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <form method="POST"
action="/map-edit-pertanian/{{ $data->tahun }}/{{ $data->kecamatan }}">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">
            Edit Data Pertanian - {{ $data->kecamatan }}
          </h5>
        </div>

        <div class="modal-body">

    <div class="section-card">

        <div class="section-title">
            🌾 Data Padi
        </div>

        <label class="form-label">Luas Panen (Ha)</label>
        <input name="padi_luas_panen"
            value="{{ $data->padi_luas_panen }}"
            class="form-control mb-3">

        <label class="form-label">Produksi (Ton)</label>
        <input name="padi_produksi"
            value="{{ $data->padi_produksi }}"
            class="form-control mb-3">

        <label class="form-label">Produktivitas (Ton/Ha)</label>
        <input name="padi_produktivitas"
            value="{{ $data->padi_produktivitas }}"
            class="form-control">

    </div>

    <div class="section-card">

        <div class="section-title">
            🌽 Data Jagung
        </div>

        <label class="form-label">Luas Panen (Ha)</label>
        <input name="jagung_luas_panen"
            value="{{ $data->jagung_luas_panen }}"
            class="form-control mb-3">

        <label class="form-label">Produksi (Ton)</label>
        <input name="jagung_produksi"
            value="{{ $data->jagung_produksi }}"
            class="form-control mb-3">

        <label class="form-label">Produktivitas (Ton/Ha)</label>
        <input name="jagung_produktivitas"
            value="{{ $data->jagung_produktivitas }}"
            class="form-control">

    </div>

    <div class="section-card">

        <div class="section-title">
            🫘 Data Kedelai
        </div>

        <label class="form-label">Luas Panen (Ha)</label>
        <input name="kedelai_luas_panen"
            value="{{ $data->kedelai_luas_panen }}"
            class="form-control mb-3">

        <label class="form-label">Produksi (Ton)</label>
        <input name="kedelai_produksi"
            value="{{ $data->kedelai_produksi }}"
            class="form-control mb-3">

        <label class="form-label">Produktivitas (Ton/Ha)</label>
        <input name="kedelai_produktivitas"
            value="{{ $data->kedelai_produktivitas }}"
            class="form-control">

    </div>

</div>

        <div class="modal-footer">

    <button type="button"
        class="btn btn-outline-secondary btn-cancel"
        data-bs-dismiss="modal">
        ❌ Batal
    </button>

    <button class="btn btn-save text-white">
        💾 Simpan Perubahan
    </button>

</div>

      </form>

    </div>
  </div>
</div>

@endsection


@push('scripts')

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

/* INIT MAP */
var map = L.map('map').setView([-7.1, 110.9], 10);

/* BASE MAP */
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
}).addTo(map);

/* KECAMATAN TERPILIH */
let selectedName = "{{ $data->kecamatan }}";

/* LOAD GEOJSON */
fetch("/geojson-kecamatan")
.then(r => r.json())
.then(data => {

    let selectedLayer = null;

    let layerGroup = L.geoJSON(data, {

        style: function(feature) {

            let aktif =
                feature.properties.kecamatan === selectedName;

            return {
                color: aktif ? "yellow" : "#999",
                weight: aktif ? 5 : 1,
                fillColor: aktif ? "#ffeb3b" : "#4CAF50",
                fillOpacity: aktif ? 0.7 : 0.2
            };
        },

        onEachFeature: function(feature, layer) {

            let nama = feature.properties.kecamatan;

            if (nama !== selectedName) {
                layer.options.interactive = false;
            }

            if (nama === selectedName) {

                selectedLayer = layer;

                layer.on("click", function() {
                    new bootstrap.Modal(
                        document.getElementById('editModal')
                    ).show();
                });
            }
        }

    }).addTo(map);

    /* FIT KE AREA */
    map.fitBounds(layerGroup.getBounds());

    /* PASTIKAN POLYGON AKTIF PALING DEPAN */
    if (selectedLayer) {
        selectedLayer.bringToFront();
    }

});

</script>

@endpush
