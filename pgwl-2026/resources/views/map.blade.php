@extends('layouts.template')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map {
            height: calc(100vh - 56px);
            width: 100%;
        }

        .leaflet-interactive:focus {
            outline: none !important;
        }

        .my-tooltip {
            background: rgba(0, 0, 0, 0.75);
            color: #fff;
            border: none;
            box-shadow: none;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 12px;
        }

        .legend {
            background: rgba(255, 255, 255, 0.92);
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 11px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            line-height: 1.4;
        }

        .legend-title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .legend i {
            width: 8px;
            height: 8px;
            display: inline-block;
            margin-right: 5px;
        }

        /* Legend choropleth pakai kotak lebih besar */
        .legend-choropleth i {
            width: 14px;
            height: 14px;
            display: inline-block;
            margin-right: 6px;
            border-radius: 2px;
            vertical-align: middle;
        }

        .btn-hapus-map {
            margin-top: 8px;
            background: #e74c3c;
            color: #fff;
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }

        .btn-hapus-map:hover {
            background: #c0392b;
            transform: scale(1.05);
        }

        .btn-hapus-map i {
            font-size: 12px;
        }

        #modalLahan {
            z-index: 9999 !important;
        }

        .modal-backdrop {
            z-index: 9998 !important;
        }

        #modalLahan .modal-dialog {
            margin-top: 80px;
        }

        /* POPUP LEAFLET */

        .leaflet-popup-content-wrapper {
            border-radius: 14px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .18);
        }

        .leaflet-popup-content {
            margin: 0;
            min-width: 260px;
            font-family: 'Inter', sans-serif;
        }

        .popup-header {
            background: linear-gradient(135deg, #2e7d32, #43a047);
            color: #fff;
            padding: 12px 15px;
        }

        .popup-header h6 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
        }

        .popup-body {
            padding: 12px 15px;
        }

        .popup-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .popup-item i {
            color: #2e7d32;
            width: 16px;
            margin-top: 2px;
        }

        .popup-img {
            width: 100%;
            border-radius: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }

        .popup-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .popup-actions .btn {
            flex: 1;
        }

        /* MODAL LAHAN */

        #modalLahan .modal-dialog {
            max-width: 550px;
        }

        #modalLahan .modal-content {
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .2);
        }

        #modalLahan .modal-header {
            background: linear-gradient(135deg, #2E7D32, #43A047);
            color: white;
            border: none;
            padding: 18px 22px;
        }

        #modalLahan .modal-header h5 {
            margin: 0;
            font-weight: 700;
        }

        #modalLahan .modal-body {
            padding: 22px;
            background: #fafafa;
        }

        #modalLahan .input-group-text {
            background: #f1f8f2;
            color: #2E7D32;
            border-color: #d8e8da;
        }

        #modalLahan .form-control {
            border-radius: 0 10px 10px 0;
        }

        #modalLahan .form-control:focus {
            border-color: #43A047;
            box-shadow: 0 0 0 .15rem rgba(67, 160, 71, .15);
        }

        #modalLahan .modal-footer {
            border: none;
            padding: 18px 22px;
        }

        #modalLahan .btn-success {
            background: #2E7D32;
            border: none;
        }

        #modalLahan .btn-success:hover {
            background: #256428;
        }

        #preview-image {
            box-shadow: 0 4px 12px rgba(0, 0, 0, .12);
        }

        .popup-header{
    background:linear-gradient(135deg,#2E7D32,#43A047);
    color:white;
    padding:12px 15px;
}

.popup-header h6{
    margin:0;
    font-size:15px;
    font-weight:700;
}

.popup-body{
    padding:12px 15px;
}

.popup-item{
    display:flex;
    gap:8px;
    margin-bottom:10px;
    font-size:13px;
}

.popup-item i{
    color:#2E7D32;
    width:18px;
    margin-top:3px;
}

.popup-actions{
    display:flex;
    gap:8px;
    margin-top:12px;
}

.popup-actions .btn{
    flex:1;
    border-radius:8px;
}

.leaflet-popup-content-wrapper{
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,.18);
}

.leaflet-popup-content{
    margin:0;
    min-width:280px;
}

    </style>
@endsection

@section('content')
    <div id="map"></div>

    <div class="modal fade" id="modalLahan" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">
                    <h5>
                        <i class="fa-solid fa-seedling me-2"></i>
                        Tambah Lahan Pertanian
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        onclick="batalDigitasi()">
                    </button>
                </div>

                <div class="modal-body">

                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input class="form-control" id="nama_pemilik" placeholder="Nama Pemilik">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>
                        <input class="form-control" id="kecamatan" placeholder="Kecamatan">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa-solid fa-seedling"></i>
                        </span>
                        <input class="form-control" id="komoditas" placeholder="Komoditas">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa-solid fa-ruler-combined"></i>
                        </span>
                        <input class="form-control" id="luas_lahan" placeholder="Luas Lahan (Ha)">
                    </div>

                    <label class="form-label fw-semibold">
                        <i class="fa-solid fa-image"></i>
                        Foto Lahan
                    </label>

                    <input type="file" id="image" class="form-control" onchange="previewImage(event)">

                    <div class="mt-3 text-center">

                        <img id="preview-image" src=""
                            style="
                            display:none;
                            width:100%;
                            max-height:250px;
                            object-fit:cover;
                            border-radius:12px;
                            border:1px solid #ddd;
                        ">

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-outline-secondary" onclick="batalDigitasi()">

                        <i class="fa-solid fa-xmark"></i>
                        Batal

                    </button>

                    <button type="button" class="btn btn-success" onclick="simpanLahan()">

                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan Data

                    </button>

                </div>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        /*  MAP INIT */
        var map = L.map('map').setView([-7.1, 110.9], 10);

        // URUTAN LAYER
        map.createPane('choroplethPane');
        map.createPane('boundaryPane');
        map.createPane('chartPane');

        map.getPane('choroplethPane').style.zIndex = 400;
        map.getPane('boundaryPane').style.zIndex = 500;
        map.getPane('chartPane').style.zIndex = 600;

        var overlayMaps = {};


        // =======================
        // LAYER DIGITASI
        // =======================

        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var lahanPertanianLayer = L.geoJSON(null, {

            style: {
                color: "#ff0000",
                weight: 2,
                fillOpacity: 0.4
            },

            onEachFeature: function(feature, layer) {


                let img = '';

                if (feature.properties.image) {

                    img = `
                <img
                    src="/storage/images/${feature.properties.image}"
                    width="220"
                    class="img-fluid rounded mt-2">
            `;
                }



                layer.bindPopup(`

<div class="popup-header">
    <h6>Lahan Pertanian</h6>
</div>

<div class="popup-body">

    <div class="popup-item">
        <i class="fa-solid fa-user"></i>
        <span><b>Nama Pemilik:</b> ${feature.properties.nama_pemilik}</span>
    </div>

    <div class="popup-item">
        <i class="fa-solid fa-location-dot"></i>
        <span><b>Kecamatan:</b> ${feature.properties.kecamatan}</span>
    </div>

    <div class="popup-item">
        <i class="fa-solid fa-seedling"></i>
        <span><b>Komoditas:</b> ${feature.properties.komoditas}</span>
    </div>

    <div class="popup-item">
        <i class="fa-solid fa-ruler-combined"></i>
        <span><b>Luas:</b> ${feature.properties.luas_lahan} Ha</span>
    </div>

    ${img}

    <div class="popup-actions">

        <a href="/map-edit-lahan/${feature.properties.id}"
           class="btn btn-warning btn-sm">
            <i class="fa fa-pen"></i>
            Edit
        </a>

        <button
            class="btn btn-danger btn-sm"
            onclick="hapusLahan(${feature.properties.id})">

            <i class="fa fa-trash"></i>
            Hapus

        </button>

    </div>

</div>
`);

                layer.bindTooltip(feature.properties.kecamatan, {
                    sticky: true,
                    direction: "top",
                    className: "my-tooltip"
                });

            }



        }).addTo(map);

        var batasAdminLahan = L.geoJSON(null, {
            style: {
                color: "#2E7D32",
                weight: 1.5,
                fillOpacity: 0.1
            },

            onEachFeature: function(feature, layer) {

                // TOOLTIP kecamatan
                layer.bindTooltip(
                    feature.properties.kecamatan, {
                        sticky: true,
                        direction: "center",
                        className: "my-tooltip"
                    }
                );
            }
        });

        $.getJSON("{{ route('geojson.kecamatan') }}", function(data) {

            batasAdminLahan.addData(data);

            // langsung tambahkan ke map / group layer
            lahanPertanianLayer.addLayer(batasAdminLahan);
        });

        var osm = L.tileLayer(
            "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 19,
                attribution: "&copy; OpenStreetMap"
            }
        );

        var imagery = L.tileLayer(
            "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", {
                attribution: "Tiles © Esri"
            }
        );

        osm.addTo(map);

        // =======================
        // DRAW CONTROL
        // =======================

        var drawControl = new L.Control.Draw({

            draw: {
                polygon: true,
                rectangle: true,
                polyline: false,
                circle: false,
                marker: false,
                circlemarker: false
            },

            edit: {
                featureGroup: drawnItems
            }
        });

        map.addControl(drawControl);





        /* =======================
           FUNGSI WARNA CHOROPLETH
        ======================= */
        function getColorPadi(v) {
            return v > 8 ? '#005a32' :
                v > 6 ? '#238b45' :
                v > 4 ? '#41ae76' :
                v > 2 ? '#74c476' :
                v > 0 ? '#bae4b3' :
                '#edf8e9';
        }

        function getColorJagung(v) {
            return v > 8 ? '#7f2704' :
                v > 6 ? '#d94801' :
                v > 4 ? '#fd8d3c' :
                v > 2 ? '#fdbe85' :
                v > 0 ? '#feedde' :
                '#fff5eb';
        }


        function getColorKedelai(v) {
            return v > 3.0 ? '#084594' :
                v > 2.5 ? '#2171b5' :
                v > 2.0 ? '#4292c6' :
                v > 1.5 ? '#6baed6' :
                v > 0 ? '#bdd7e7' :
                '#eff3ff';
        }

        /*  KECAMATAN LAYER */
        var kecamatan = L.geoJSON(null, {
            pane: 'boundaryPane',
            style: function() {
                return {
                    color: "#000",
                    weight: 1.5,
                    fillColor: "#4CAF50",
                    fillOpacity: 0.5
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(
                    "<b>Kecamatan:</b> " + feature.properties.kecamatan +
                    "<hr><b>🌾 Padi</b>" +
                    "<br>Luas Panen: " + feature.properties.padi_luas_panen +
                    "<br>Produksi: " + feature.properties.padi_produksi +
                    "<br>Produktivitas: " + feature.properties.padi_produktivitas +
                    "<hr><b>🌽 Jagung</b>" +
                    "<br>Luas Panen: " + feature.properties.jagung_luas_panen +
                    "<br>Produksi: " + feature.properties.jagung_produksi +
                    "<br>Produktivitas: " + feature.properties.jagung_produktivitas +
                    "<hr><b>🫘 Kedelai</b>" +
                    "<br>Luas Panen: " + feature.properties.kedelai_luas_panen +
                    "<br>Produksi: " + feature.properties.kedelai_produksi +
                    "<br>Produktivitas: " + feature.properties.kedelai_produktivitas + "<hr>" +

                    "<br><a href='/map-edit-pertanian/" +
                    feature.properties.tahun +
                    "/" +
                    encodeURIComponent(feature.properties.kecamatan) +
                    "' " +
                    "style='display:inline-block;margin-top:5px;padding:6px 8px;background:#ff9800;color:#fff;border-radius:5px;text-decoration:none'>" +
                    "<i class='fa fa-edit'></i> Edit Data</a>" +

                    "<br><br>" +

                    "<a href='/pertanian/create/" +
                    encodeURIComponent(feature.properties.kecamatan) +
                    "' style='display:inline-block;padding:6px 8px;background:#28a745;color:#fff;border-radius:5px;text-decoration:none'>" +
                    "➕ Create Tahun Baru</a>" +

                    "<hr>" +

                    // 🔥 TOMBOL DELETE BARU
                    "<button onclick=\"hapusKecamatan('" +
                    feature.properties.kecamatan +
                    "')\" style='background:#e74c3c;color:#fff;border:none;padding:6px 10px;border-radius:5px;cursor:pointer'>" +
                    "🗑️ Hapus Kecamatan</button>"
                );
                layer.bindTooltip(feature.properties.kecamatan, {
                    sticky: true,
                    direction: "top",
                    className: "my-tooltip"
                });


            }
        });

        /* =======================
           CHOROPLETH LAYERS
        ======================= */
        var choroplethPadi = L.geoJSON(null, {
            pane: 'choroplethPane',
            style: function(feature) {
                return {
                    color: "#555",
                    weight: 1,
                    fillColor: getColorPadi(safe(feature.properties.padi_produktivitas)),
                    fillOpacity: 0.75
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(
                    "<b>Kecamatan:</b> " + feature.properties.kecamatan +
                    "<hr><b>🌾 Padi</b>" +
                    "<br>Luas Panen: " + feature.properties.padi_luas_panen +
                    "<br>Produksi: " + feature.properties.padi_produksi +
                    "<br>Produktivitas: " + feature.properties.padi_produktivitas
                );
                layer.bindTooltip(feature.properties.kecamatan, {
                    sticky: true,
                    direction: "top",
                    className: "my-tooltip"
                });
            }
        });

        var choroplethJagung = L.geoJSON(null, {
            pane: 'choroplethPane',
            style: function(feature) {
                return {
                    color: "#555",
                    weight: 1,
                    fillColor: getColorJagung(safe(feature.properties.jagung_produktivitas)),
                    fillOpacity: 0.75
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(
                    "<b>Kecamatan:</b> " + feature.properties.kecamatan +
                    "<hr><b>🌽 Jagung</b>" +
                    "<br>Luas Panen: " + feature.properties.jagung_luas_panen +
                    "<br>Produksi: " + feature.properties.jagung_produksi +
                    "<br>Produktivitas: " + feature.properties.jagung_produktivitas
                );
                layer.bindTooltip(feature.properties.kecamatan, {
                    sticky: true,
                    direction: "top",
                    className: "my-tooltip"
                });
            }
        });

        var choroplethKedelai = L.geoJSON(null, {
            pane: 'choroplethPane',
            style: function(feature) {
                return {
                    color: "#555",
                    weight: 1,
                    fillColor: getColorKedelai(safe(feature.properties.kedelai_produktivitas)),
                    fillOpacity: 0.75
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(
                    "<b>Kecamatan:</b> " + feature.properties.kecamatan +
                    "<hr><b>🫘 Kedelai</b>" +
                    "<br>Luas Panen: " + feature.properties.kedelai_luas_panen +
                    "<br>Produksi: " + feature.properties.kedelai_produksi +
                    "<br>Produktivitas: " + feature.properties.kedelai_produktivitas
                );
                layer.bindTooltip(feature.properties.kecamatan, {
                    sticky: true,
                    direction: "top",
                    className: "my-tooltip"
                });
            }
        });

        /*
           CHART LAYERS
        ======================= */
        var luasChartLayer = L.layerGroup();
        var produksiChartLayer = L.layerGroup();
        var produktivitasChartLayer = L.layerGroup();



        /* =======================
           SAFE
        ======================= */
        function safe(v) {
            return (v == null || isNaN(v)) ? 0 : parseFloat(v);
        }

        function bar(value, max, color) {
            let h = max === 0 ? 0 : (value / max) * 35;
            return `<div style="
        width:8px;
        height:${h}px;
        background:${color};
        margin:0 1px;
    "></div>`;
        }

        /* =======================
           BAR
        ======================= */
        function rebuildBarChart(data) {

            luasChartLayer.clearLayers();
            produksiChartLayer.clearLayers();
            produktivitasChartLayer.clearLayers();

            data.features.forEach(function(f) {

                var center = L.geoJSON(f).getBounds().getCenter();
                var nama = f.properties.kecamatan;

                var pL = safe(f.properties.padi_luas_panen);
                var jL = safe(f.properties.jagung_luas_panen);
                var kL = safe(f.properties.kedelai_luas_panen);
                var maxL = Math.max(pL, jL, kL);

                luasChartLayer.addLayer(
                    L.marker(center, {
                        icon: L.divIcon({
                            className: "",
                            html: `
                    <div style="background:#fff;padding:4px;border-radius:6px;width:90px;text-align:center">
                        <div style="font-size:10px;font-weight:bold">${nama}</div>
                        <div style="display:flex;align-items:end;height:35px;justify-content:center">
                            ${bar(pL,maxL,"#4CAF50")}
                            ${bar(jL,maxL,"#FFC107")}
                            ${bar(kL,maxL,"#2196F3")}
                        </div>
                    </div>`
                        })
                    })
                );

            });
        }


        function popupPertanian(feature) {

            return `

<div class="popup-header">
    <h6>
        <i class="fa-solid fa-wheat-awn"></i>
        ${feature.properties.kecamatan}
    </h6>
</div>

<div class="popup-body">

    <div class="popup-item">
        <i class="fa-solid fa-calendar"></i>
        <span><b>Tahun:</b> ${feature.properties.tahun}</span>
    </div>

    <hr>

    <div class="popup-item">
        <i class="fa-solid fa-seedling"></i>
        <span>
            <b>Padi</b><br>
            Luas Panen : ${feature.properties.padi_luas_panen} Ha<br>
            Produksi : ${feature.properties.padi_produksi} Ton<br>
            Produktivitas : ${feature.properties.padi_produktivitas}
        </span>
    </div>

    <div class="popup-item">
        <i class="fa-solid fa-wheat-awn"></i>
        <span>
            <b>Jagung</b><br>
            Luas Panen : ${feature.properties.jagung_luas_panen} Ha<br>
            Produksi : ${feature.properties.jagung_produksi} Ton<br>
            Produktivitas : ${feature.properties.jagung_produktivitas}
        </span>
    </div>

    <div class="popup-item">
        <i class="fa-solid fa-leaf"></i>
        <span>
            <b>Kedelai</b><br>
            Luas Panen : ${feature.properties.kedelai_luas_panen} Ha<br>
            Produksi : ${feature.properties.kedelai_produksi} Ton<br>
            Produktivitas : ${feature.properties.kedelai_produktivitas}
        </span>
    </div>

    <div class="popup-actions">

        <a href="/map-edit-pertanian/${feature.properties.tahun}/${encodeURIComponent(feature.properties.kecamatan)}"
           class="btn btn-warning btn-sm">

            <i class="fa-solid fa-pen"></i>
            Edit

        </a>

        <a href="/pertanian/create/${encodeURIComponent(feature.properties.kecamatan)}"
           class="btn btn-success btn-sm">

            <i class="fa-solid fa-plus"></i>
            Create

        </a>

    </div>

    <div class="popup-actions">

        <button
            class="btn btn-danger btn-sm w-100"
            onclick="hapusData('${feature.properties.tahun}','${feature.properties.kecamatan}')">

            <i class="fa-solid fa-trash"></i>
            Hapus

        </button>

    </div>

</div>

`;
        }

        var tahunLayers = {};

        fetch('/tahun-pertanian')
            .then(res => res.json())
            .then(function(tahunList) {
                tahunList.sort((a, b) => b - a);

                tahunList.forEach(function(tahun) {

                    tahunLayers[tahun] = {

                        kecamatan: L.geoJSON(null, {
                            pane: 'boundaryPane',
                            style: {
                                color: "#000",
                                weight: 1.5,
                                fillColor: "#4CAF50",
                                fillOpacity: 0.5
                            },

                            onEachFeature: function(feature, layer) {

                                layer.bindPopup(
                                    popupPertanian(feature)
                                );

                                layer.bindTooltip(
                                    feature.properties.kecamatan, {
                                        sticky: true,
                                        direction: "top",
                                        className: "my-tooltip"
                                    }
                                );
                            }
                        }),

                        padi: L.geoJSON(null, {
                            pane: 'choroplethPane',
                            style: function(feature) {
                                return {
                                    color: "#555",
                                    weight: 1,
                                    fillColor: getColorPadi(
                                        safe(feature.properties.padi_produktivitas)
                                    ),
                                    fillOpacity: 0.75
                                };
                            },

                            onEachFeature: function(feature, layer) {

                                layer.bindPopup(
                                    popupPertanian(feature)
                                );

                                layer.bindTooltip(
                                    feature.properties.kecamatan, {
                                        sticky: true,
                                        direction: "top",
                                        className: "my-tooltip"
                                    }
                                );
                            }
                        }),

                        jagung: L.geoJSON(null, {
                            pane: 'choroplethPane',
                            style: function(feature) {
                                return {
                                    color: "#555",
                                    weight: 1,
                                    fillColor: getColorJagung(
                                        safe(feature.properties.jagung_produktivitas)
                                    ),
                                    fillOpacity: 0.75
                                };
                            },

                            onEachFeature: function(feature, layer) {

                                layer.bindPopup(
                                    popupPertanian(feature)
                                );

                                layer.bindTooltip(
                                    feature.properties.kecamatan, {
                                        sticky: true,
                                        direction: "top",
                                        className: "my-tooltip"
                                    }
                                );
                            }
                        }),

                        kedelai: L.geoJSON(null, {
                            pane: 'choroplethPane',
                            style: function(feature) {
                                return {
                                    color: "#555",
                                    weight: 1,
                                    fillColor: getColorKedelai(
                                        safe(feature.properties.kedelai_produktivitas)
                                    ),
                                    fillOpacity: 0.75
                                };
                            },

                            onEachFeature: function(feature, layer) {

                                layer.bindPopup(
                                    popupPertanian(feature)
                                );

                                layer.bindTooltip(
                                    feature.properties.kecamatan, {
                                        sticky: true,
                                        direction: "top",
                                        className: "my-tooltip"
                                    }
                                );
                            }

                        }),

                        luas: L.layerGroup(),
                        produksi: L.layerGroup(),
                        produktivitas: L.layerGroup()
                    };

                    $.getJSON('/geojson-kecamatan-tahun/' + tahun, function(data) {

                        tahunLayers[tahun].kecamatan.clearLayers();
                        tahunLayers[tahun].padi.clearLayers();
                        tahunLayers[tahun].jagung.clearLayers();
                        tahunLayers[tahun].kedelai.clearLayers();

                        tahunLayers[tahun].luas.clearLayers();
                        tahunLayers[tahun].produksi.clearLayers();
                        tahunLayers[tahun].produktivitas.clearLayers();

                        // ======================
                        // GEOJSON
                        // ======================
                        tahunLayers[tahun].kecamatan.addData(data);
                        tahunLayers[tahun].padi.addData(data);
                        tahunLayers[tahun].jagung.addData(data);
                        tahunLayers[tahun].kedelai.addData(data);

                        // ======================
                        // BAR CHART PER TAHUN
                        // ======================
                        data.features.forEach(function(f) {

                            var center = L.geoJSON(f).getBounds().getCenter();
                            var nama = f.properties.kecamatan;

                            // LUAS PANEN
                            var pL = safe(f.properties.padi_luas_panen);
                            var jL = safe(f.properties.jagung_luas_panen);
                            var kL = safe(f.properties.kedelai_luas_panen);

                            // PRODUKSI
                            var pP = safe(f.properties.padi_produksi);
                            var jP = safe(f.properties.jagung_produksi);
                            var kP = safe(f.properties.kedelai_produksi);

                            // PRODUKTIVITAS
                            var pR = safe(f.properties.padi_produktivitas);
                            var jR = safe(f.properties.jagung_produktivitas);
                            var kR = safe(f.properties.kedelai_produktivitas);

                            var maxL = Math.max(pL, jL, kL);
                            var maxP = Math.max(pP, jP, kP);
                            var maxR = Math.max(pR, jR, kR);

                            // ======================
                            // CHART LUAS PANEN
                            // ======================
                            tahunLayers[tahun].luas.addLayer(
                                L.marker(center, {
                                    pane: 'chartPane',
                                    icon: L.divIcon({
                                        className: "",
                                        html: `
<div style="
    background:#fff;
    padding:4px;
    border-radius:5px;
    width:70px;
    text-align:center;
">
    <div style="font-size:9px;font-weight:bold">
        ${nama}
    </div>

    <div style="
        display:flex;
        align-items:end;
        justify-content:center;
        height:30px;
    ">
        ${bar(pL,maxL,"#4CAF50")}
        ${bar(jL,maxL,"#FFC107")}
        ${bar(kL,maxL,"#2196F3")}
    </div>

    <div style="
        display:flex;
        justify-content:center;
        margin-top:3px;
        font-size:7px;
    ">
        <div style="width:19px;text-align:center">${pL}</div>
        <div style="width:19px;text-align:center">${jL}</div>
        <div style="width:19px;text-align:center">${kL}</div>
    </div>
</div>
`
                                    })
                                })
                            );

                            // ======================
                            // CHART PRODUKSI
                            // ======================
                            tahunLayers[tahun].produksi.addLayer(
                                L.marker(center, {
                                    pane: 'chartPane',
                                    icon: L.divIcon({
                                        className: "",
                                        html: `
<div style="
    background:#fff;
    padding:4px;
    border-radius:5px;
    width:70px;
    text-align:center;
">
    <div style="font-size:9px;font-weight:bold">
        ${nama}
    </div>

    <div style="
        display:flex;
        align-items:end;
        justify-content:center;
        height:30px;
    ">
        ${bar(pP,maxP,"#4CAF50")}
        ${bar(jP,maxP,"#FFC107")}
        ${bar(kP,maxP,"#2196F3")}
    </div>

    <div style="
        display:flex;
        justify-content:center;
        margin-top:3px;
        font-size:7px;
    ">
        <div style="width:22px;text-align:center">${pP}</div>
        <div style="width:22px;text-align:center">${jP}</div>
        <div style="width:22px;text-align:center">${kP}</div>
    </div>
</div>
`
                                    })
                                })
                            );

                            // ======================
                            // CHART PRODUKTIVITAS
                            // ======================
                            tahunLayers[tahun].produktivitas.addLayer(
                                L.marker(center, {
                                    pane: 'chartPane',
                                    icon: L.divIcon({
                                        className: "",
                                        html: `
<div style="
    background:#fff;
    padding:4px;
    border-radius:5px;
    width:70px;
    text-align:center;
">
    <div style="font-size:9px;font-weight:bold">
        ${nama}
    </div>

    <div style="
        display:flex;
        align-items:end;
        justify-content:center;
        height:30px;
    ">
        ${bar(pR,maxR,"#4CAF50")}
        ${bar(jR,maxR,"#FFC107")}
        ${bar(kR,maxR,"#2196F3")}
    </div>

    <div style="
        display:flex;
        justify-content:center;
        margin-top:3px;
        font-size:7px;
    ">
        <div style="width:19px;text-align:center">${pR}</div>
        <div style="width:19px;text-align:center">${jR}</div>
        <div style="width:19px;text-align:center">${kR}</div>
    </div>
</div>
`
                                    })
                                })
                            );
                        });

                        // ======================
                        // FIT BOUNDS
                        // ======================
                        if (tahunLayers[tahun].kecamatan.getLayers().length > 0) {
                            map.fitBounds(tahunLayers[tahun].kecamatan.getBounds());
                        }

                    });

                    overlayMaps["🌾 Lahan Pertanian"] =
                        lahanPertanianLayer;

                    overlayMaps["<span class='tahun-group' data-tahun='" + tahun + "'>📅 TAHUN " + tahun +
                        "</span>"] = L.layerGroup();

                    overlayMaps["🗺️ Pertanian Kabupaten Grobogan (" + tahun + ")"] =
                        tahunLayers[tahun].kecamatan;

                    overlayMaps["🌾 Choropleth Padi (" + tahun + ")"] =
                        tahunLayers[tahun].padi;

                    overlayMaps["🌽 Choropleth Jagung (" + tahun + ")"] =
                        tahunLayers[tahun].jagung;

                    overlayMaps["🫘 Choropleth Kedelai (" + tahun + ")"] =
                        tahunLayers[tahun].kedelai;

                    overlayMaps["🟩 Bar Chart Luas Panen (" + tahun + ")"] =
                        tahunLayers[tahun].luas;

                    overlayMaps["📦 Bar Chart Produksi (" + tahun + ")"] =
                        tahunLayers[tahun].produksi;

                    overlayMaps["📊 Bar Chart Produktivitas (" + tahun + ")"] =
                        tahunLayers[tahun].produktivitas;

                });

                fetch('/lahan-pertanian')
                    .then(res => res.json())
                    .then(data => {

                        lahanPertanianLayer.addData(data);

                    });

                var layerControl = L.control.layers(baseMaps, overlayMaps).addTo(map);

                setTimeout(function() {

                    document.querySelectorAll(
                        '.leaflet-control-layers-overlays label'
                    ).forEach(function(label) {

                        if (label.innerText.includes('TAHUN')) {

                            label.style.marginTop = '12px';
                            label.style.marginBottom = '6px';
                            label.style.fontWeight = 'bold';
                            label.style.borderTop = '1px solid #ddd';
                            label.style.paddingTop = '8px';

                            label.style.cursor = 'pointer';

                            label.addEventListener('click', function(e) {

                                let tahun =
                                    this.innerText.match(/\d{4}/)[0];

                                let parentCheckbox =
                                    this.querySelector('input[type=checkbox]');

                                setTimeout(function() {

                                    let checked = parentCheckbox.checked;

                                    document.querySelectorAll(
                                        '.leaflet-control-layers-overlays label'
                                    ).forEach(function(lbl) {

                                        if (
                                            lbl.innerText.includes('(' + tahun +
                                                ')')
                                        ) {

                                            let cb =
                                                lbl.querySelector(
                                                    'input[type=checkbox]');

                                            if (!cb) return;

                                            // jika TAHUN dicentang
                                            if (checked && !cb.checked) {
                                                cb.click();
                                            }

                                            // jika TAHUN di-uncheck
                                            if (!checked && cb.checked) {
                                                cb.click();
                                            }

                                        }

                                    });

                                }, 10);

                            });

                        }

                    });

                }, 1000);

            });

        /* =======================
           LOAD GEOJSON
        ======================= */
        $.getJSON("{{ route('geojson.kecamatan') }}", function(data) {

            kecamatan.addData(data);
            //kecamatan.addTo(map);
            map.fitBounds(kecamatan.getBounds());

            // tambahkan data ke choropleth
            choroplethPadi.addData(data);
            choroplethJagung.addData(data);
            choroplethKedelai.addData(data);

            data.features.forEach(function(f) {
                var center = L.geoJSON(f).getBounds().getCenter();
                var nama = f.properties.kecamatan;

                var pL = safe(f.properties.padi_luas_panen);
                var jL = safe(f.properties.jagung_luas_panen);
                var kL = safe(f.properties.kedelai_luas_panen);

                var pP = safe(f.properties.padi_produksi);
                var jP = safe(f.properties.jagung_produksi);
                var kP = safe(f.properties.kedelai_produksi);

                var pR = safe(f.properties.padi_produktivitas);
                var jR = safe(f.properties.jagung_produktivitas);
                var kR = safe(f.properties.kedelai_produktivitas);

                var maxL = Math.max(pL, jL, kL);
                var maxP = Math.max(pP, jP, kP);
                var maxR = Math.max(pR, jR, kR);

                luasChartLayer.addLayer(L.marker(center, {
                    icon: L.divIcon({
                        className: "",
                        html: `<div style="background:#fff;padding:4px;border-radius:5px;width:80px;text-align:center">
                <div style="font-size:9px;font-weight:bold">${nama}</div>
                <div style="display:flex;align-items:end;height:30px;justify-content:center">
                    ${bar(pL,maxL,"#4CAF50")}${bar(jL,maxL,"#FFC107")}${bar(kL,maxL,"#2196F3")}
                </div></div>`
                    })
                }));

                produksiChartLayer.addLayer(L.marker(center, {
                    icon: L.divIcon({
                        className: "",
                        html: `<div style="background:#fff;padding:4px;border-radius:5px;width:80px;text-align:center">
                <div style="font-size:9px;font-weight:bold">${nama}</div>
                <div style="display:flex;align-items:end;height:30px;justify-content:center">
                    ${bar(pP,maxP,"#4CAF50")}${bar(jP,maxP,"#FFC107")}${bar(kP,maxP,"#2196F3")}
                </div></div>`
                    })
                }));

                produktivitasChartLayer.addLayer(L.marker(center, {
                    icon: L.divIcon({
                        className: "",
                        html: `<div style="background:#fff;padding:4px;border-radius:5px;width:80px;text-align:center">
                <div style="font-size:9px;font-weight:bold">${nama}</div>
                <div style="display:flex;align-items:end;height:30px;justify-content:center">
                    ${bar(pR,maxR,"#4CAF50")}${bar(jR,maxR,"#FFC107")}${bar(kR,maxR,"#2196F3")}
                </div></div>`
                    })
                }));
            });
        });

        /* =======================
           BASE + OVERLAY
        ======================= */
        var baseMaps = {
            "OpenStreetMap": osm,
            "Citra Satelit": imagery
        };



        // var overlayMaps = {
        //     "🗺️ Pertanian Kabupaten Grobogan": kecamatan,
        //     "🟩 Bar Chart Luas Panen": luasChartLayer,
        //     "📦 Bar Chart Produksi": produksiChartLayer,
        //     "📊 Bar Chart Produktivitas": produktivitasChartLayer,
        //     "🌾 Choropleth Padi": choroplethPadi,
        //     "🌽 Choropleth Jagung": choroplethJagung,
        //     "🫘 Choropleth Kedelai": choroplethKedelai
        // };

        // L.control.layers(baseMaps, overlayMaps).addTo(map);

        /* =======================
           LEGEND CONTROL — WITH SCROLL
        ======================= */
        var legend = L.control({
            position: "bottomright"
        });
        var activeLayers = new Set();

        /* helper */
        function legendRow(title, content) {
            return `
        <div style="margin-bottom:8px">
            <div style="font-weight:bold;margin-bottom:4px">${title}</div>
            <div>${content}</div>
        </div>
    `;
        }

        /* build legend */
        function buildLegendContent() {

            let html = "";

            /* ===== BAR CHART ===== */
            if (
                activeLayers.has("luas") ||
                activeLayers.has("produksi") ||
                activeLayers.has("produktivitas")
            ) {
                html += legendRow("📊 Statistik Pertanian", `
            <i style="background:#4CAF50;width:10px;height:10px;display:inline-block"></i> Padi<br>
            <i style="background:#FFC107;width:10px;height:10px;display:inline-block"></i> Jagung<br>
            <i style="background:#2196F3;width:10px;height:10px;display:inline-block"></i> Kedelai
        `);
            }

            /* ===== PADI ===== */
            if (activeLayers.has("padi")) {
                html += legendRow("🌾 Padi", `
            <i style="background:#edf8e9;width:12px;height:12px;display:inline-block"></i> 0 - 2<br>
            <i style="background:#bae4b3;width:12px;height:12px;display:inline-block"></i> 2 - 4<br>
            <i style="background:#74c476;width:12px;height:12px;display:inline-block"></i> 4 - 6<br>
            <i style="background:#238b45;width:12px;height:12px;display:inline-block"></i> 6 - 8<br>
            <i style="background:#005a32;width:12px;height:12px;display:inline-block"></i> > 8
        `);
            }

            /* ===== JAGUNG ===== */
            if (activeLayers.has("jagung")) {
                html += legendRow("🌽 Jagung", `
            <i style="background:#feedde;width:12px;height:12px;display:inline-block"></i> 0 - 2<br>
            <i style="background:#fdbe85;width:12px;height:12px;display:inline-block"></i> 2 - 4<br>
            <i style="background:#fd8d3c;width:12px;height:12px;display:inline-block"></i> 4 - 6<br>
            <i style="background:#d94801;width:12px;height:12px;display:inline-block"></i> 6 - 8<br>
            <i style="background:#7f2704;width:12px;height:12px;display:inline-block"></i> > 8
        `);
            }

            /* ===== KEDELAI ===== */
            if (activeLayers.has("kedelai")) {
                html += legendRow("🫘 Kedelai", `
            <i style="background:#eff3ff;width:12px;height:12px;display:inline-block"></i> 0 - 1.5<br>
            <i style="background:#bdd7e7;width:12px;height:12px;display:inline-block"></i> 1.5 - 2.0<br>
            <i style="background:#6baed6;width:12px;height:12px;display:inline-block"></i> 2.0 - 2.5<br>
            <i style="background:#2171b5;width:12px;height:12px;display:inline-block"></i> 2.5 - 3.0<br>
            <i style="background:#084594;width:12px;height:12px;display:inline-block"></i> > 3.0
        `);
            }

            return html;
        }

        /* refresh legend */
        function refreshLegend() {
            map.removeControl(legend);

            if (activeLayers.size === 0) return;

            legend.onAdd = function() {
                var div = L.DomUtil.create("div", "legend");

                div.style.maxHeight = "220px"; // 🔥 tinggi dibatasi
                div.style.overflowY = "auto"; // 🔥 scroll aktif
                div.style.overflowX = "hidden";

                div.innerHTML = buildLegendContent();
                return div;
            };

            legend.addTo(map);
        }

        /* =======================
           EVENTS
        ======================= */
        map.on("overlayadd", function(e) {

            if (e.name.includes("Bar Chart Luas Panen")) activeLayers.add("luas");
            if (e.name.includes("Bar Chart Produksi")) activeLayers.add("produksi");
            if (e.name.includes("Bar Chart Produktivitas")) activeLayers.add("produktivitas");

            if (e.name.includes("Choropleth Padi")) activeLayers.add("padi");
            if (e.name.includes("Choropleth Jagung")) activeLayers.add("jagung");
            if (e.name.includes("Choropleth Kedelai")) activeLayers.add("kedelai");

            refreshLegend();
        });

        map.on("overlayremove", function(e) {

            if (e.name.includes("Bar Chart Luas Panen")) activeLayers.delete("luas");
            if (e.name.includes("Bar Chart Produksi")) activeLayers.delete("produksi");
            if (e.name.includes("Bar Chart Produktivitas")) activeLayers.delete("produktivitas");

            if (e.name.includes("Choropleth Padi")) activeLayers.delete("padi");
            if (e.name.includes("Choropleth Jagung")) activeLayers.delete("jagung");
            if (e.name.includes("Choropleth Kedelai")) activeLayers.delete("kedelai");

            refreshLegend();
        });

        // =======================
        // DIGITASI LAHAN
        // =======================

        var tempGeo = null;
        var lastLayer = null;


        map.on('draw:created', function(e) {

            lastLayer = e.layer;

            drawnItems.addLayer(lastLayer);

            tempGeo = lastLayer.toGeoJSON();

            lahanPertanianLayer.addLayer(lastLayer);

            $('#modalLahan').modal('show');

        });

        // function hapusData(tahun, kecamatan) {

        //     if (!confirm("Yakin hapus data ini?")) return;

        //     fetch(`/api/tabel/${tahun}/${kecamatan}`, {
        //             method: "DELETE",
        //             headers: {
        //                 "X-CSRF-TOKEN": "{{ csrf_token() }}"
        //             }
        //         })
        //         .then(res => res.json())
        //         .then(res => {
        //             alert("Data berhasil dihapus");

        //             location.reload();
        //         });
        // }

        function simpanLahan() {

            console.log(tempGeo);
            console.log($('#nama_pemilik').val());
            console.log($('#kecamatan').val());



            let formData = new FormData();

            formData.append(
                'nama_pemilik',
                $('#nama_pemilik').val()
            );



            formData.append(
                'kecamatan',
                $('#kecamatan').val()
            );

            formData.append(
                'komoditas',
                $('#komoditas').val()
            );

            formData.append(
                'luas_lahan',
                $('#luas_lahan').val()
            );

            formData.append(
                'geom',
                JSON.stringify(tempGeo.geometry)
            );

            let image =
                $('#image')[0].files[0];

            if (image) {
                formData.append(
                    'image',
                    image
                );
            }



            fetch('/lahan-pertanian', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(res => {

                    showToastSuccess('Data berhasil disimpan');

                    setTimeout(() => {
                        location.reload();
                    }, 1200);

                })
                .catch(error => {

                    console.error(error);

                    alert('Gagal menyimpan data. Lihat Console (F12)');

                });

        }

        function batalDigitasi() {

            if (lastLayer) {
                drawnItems.removeLayer(lastLayer);
            }

            $('#modalLahan').modal('hide');

            $('#nama_pemilik').val('');
            $('#kecamatan').val('');
            $('#komoditas').val('');
            $('#luas_lahan').val('');
            $('#image').val('');

            $('#preview-image')
                .attr('src', '')
                .hide();
        }

        function previewImage(event) {

            let file = event.target.files[0];

            if (!file) return;

            let preview =
                document.getElementById(
                    'preview-image'
                );

            preview.src =
                URL.createObjectURL(file);

            preview.style.display =
                'block';
        }

        function hapusLahan(id) {

            if (!window._confirmDelete) {
                window._confirmDelete = function(msg) {
                    return new Promise(resolve => {
                        let ok = confirm(msg); // sementara tetap confirm
                        resolve(ok);
                    });
                }
            }
            fetch('/lahan-pertanian/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(res => {

                    const toast = document.createElement('div');

                    toast.innerHTML = 'Data berhasil dihapus';

                    toast.style.position = 'fixed';
                    toast.style.bottom = '20px';
                    toast.style.right = '20px';
                    toast.style.zIndex = '99999';
                    toast.style.background = '#dc3545';
                    toast.style.color = '#fff';
                    toast.style.padding = '10px 16px';
                    toast.style.borderRadius = '8px';
                    toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
                    toast.style.fontSize = '14px';

                    document.body.appendChild(toast);

                    setTimeout(() => {

                        toast.remove();

                        // reload halaman peta (tanpa localhost)
                        window.location.reload();

                    }, 1500);

                });

        }

        function showToastSuccess(message) {
            const toast = document.createElement('div');

            toast.innerHTML = message;

            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '99999';
            toast.style.background = '#28a745';
            toast.style.color = '#fff';
            toast.style.padding = '10px 16px';
            toast.style.borderRadius = '8px';
            toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
            toast.style.fontSize = '14px';
            toast.style.fontWeight = '500';

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 1500);
        }

        function hapusData(tahun, kecamatan) {

            Swal.fire({
                title: 'Hapus Data?',
                text: 'Data yang dihapus tidak dapat dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (!result.isConfirmed) return;

                fetch(`/tabel/${tahun}/${encodeURIComponent(kecamatan)}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    })
                    .then(res => res.json())
                    .then(res => {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil dihapus',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            location.reload();
                        }, 1500);

                    });

            });
        }
    </script>
@endsection
