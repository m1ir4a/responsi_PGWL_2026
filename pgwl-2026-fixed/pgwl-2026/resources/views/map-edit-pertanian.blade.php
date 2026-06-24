@extends('layouts.template')

@section('content')

<div id="map" style="height:100vh;"></div>

<!-- MODAL EDIT -->
<div class="modal fade" id="editModal">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <form method="POST" action="/map-edit-pertanian/{{ $data->kecamatan }}">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">
            Edit Data Pertanian - {{ $data->kecamatan }}
          </h5>
        </div>

        <div class="modal-body">

          <!--  PADI  -->
          <h5 class="mb-2">🌾 Padi</h5>

          <label class="form-label">Luas Panen (Ha)</label>
          <input name="padi_luas_panen" value="{{ $data->padi_luas_panen }}" class="form-control mb-2">

          <label class="form-label">Produksi (Ton)</label>
          <input name="padi_produksi" value="{{ $data->padi_produksi }}" class="form-control mb-2">

          <label class="form-label">Produktivitas (Ton/Ha)</label>
          <input name="padi_produktivitas" value="{{ $data->padi_produktivitas }}" class="form-control mb-3">


          <!-- ================= JAGUNG ================= -->
          <h5 class="mb-2">🌽 Jagung</h5>

          <label class="form-label">Luas Panen (Ha)</label>
          <input name="jagung_luas_panen" value="{{ $data->jagung_luas_panen }}" class="form-control mb-2">

          <label class="form-label">Produksi (Ton)</label>
          <input name="jagung_produksi" value="{{ $data->jagung_produksi }}" class="form-control mb-2">

          <label class="form-label">Produktivitas (Ton/Ha)</label>
          <input name="jagung_produktivitas" value="{{ $data->jagung_produktivitas }}" class="form-control mb-3">


          <!-- ================= KEDELAI ================= -->
          <h5 class="mb-2">🫘 Kedelai</h5>

          <label class="form-label">Luas Panen (Ha)</label>
          <input name="kedelai_luas_panen" value="{{ $data->kedelai_luas_panen }}" class="form-control mb-2">

          <label class="form-label">Produksi (Ton)</label>
          <input name="kedelai_produksi" value="{{ $data->kedelai_produksi }}" class="form-control mb-2">

          <label class="form-label">Produktivitas (Ton/Ha)</label>
          <input name="kedelai_produktivitas" value="{{ $data->kedelai_produktivitas }}" class="form-control mb-2">

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancel
          </button>
          <button class="btn btn-primary">
            Save
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

    /* MODAL OTOMATIS MUNCUL */
    new bootstrap.Modal(document.getElementById('editModal')).show();

});

</script>

@endpush
