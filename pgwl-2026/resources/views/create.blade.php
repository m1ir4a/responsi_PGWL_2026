@extends('layouts.template')

@section('content')

<div id="map" style="height:100vh;"></div>

<!-- MODAL CREATE -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
