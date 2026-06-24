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
    </style>
@endsection

@section('content')
    <div id="map"></div>

    <div class="modal fade" id="modalLahan" tabindex="-1">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <h5>Tambah Lahan Pertanian</h5>
                </div>

                <div class="modal-body">

                    <input class="form-control mb-2" id="nama_pemilik" placeholder="Nama Pemilik">



                    <input class="form-control mb-2" id="kecamatan" placeholder="Kecamatan">

                    <input class="form-control mb-2" id="komoditas" placeholder="Komoditas">

                    <input class="form-control mb-2" id="luas_lahan" placeholder="Luas Lahan">

                    <input
    type="file"
    id="image"
    class="form-control"
    onchange="previewImage(event)">

<div class="mt-3 text-center">
    <img
        id="preview-image"
        src=""
        style="
            display:none;
            max-width:100%;
            max-height:200px;
            border-radius:8px;
            border:1px solid #ddd;
        ">
</div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" onclick="batalDigitasi()">
                        ❌ Cancel
                    </button>

                    <button type="button" class="btn btn-success" onclick="simpanLahan()">
                        💾 Simpan
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

        var lahanPertanianLayer = L.geoJSON(null,{

    style:{
        color:"#ff0000",
        weight:2,
        fillOpacity:0.4
    },

    onEachFeature:function(feature, layer){


        let img = '';

        if(feature.properties.image){

            img = `
                <img
                    src="/storage/images/${feature.properties.image}"
                    width="220"
                    class="img-fluid rounded mt-2">
            `;
        }

        layer.bindPopup(`

            <h6>
                🌾 ${feature.properties.nama_pemilik}
            </h6>

            <hr>

            <b>Kecamatan :</b>
            ${feature.properties.kecamatan}

            <br>

            <b>Komoditas :</b>
            ${feature.properties.komoditas}

            <br>

            <b>Luas :</b>
            ${feature.properties.luas_lahan} Ha

            <br>

            ${img}

            <hr>

            <a
                href="/map-edit-lahan/${feature.properties.id}"
                class="btn btn-warning btn-sm">
                ✏️ Edit
            </a>

            <button
                class="btn btn-danger btn-sm"
                onclick="hapusLahan(${feature.properties.id})">
                🗑️ Hapus
            </button>

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
            feature.properties.kecamatan,
            {
                sticky: true,
                direction: "center",
                className: "my-tooltip"
            }
        );
    }
});

$.getJSON("{{ route('geojson.kecamatan') }}", function (data) {

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

                    "<br><a href='/map-edit-pertanian/" + encodeURIComponent(feature.properties.kecamatan) +
                    "' " +
                    "style='display:inline-block;margin-top:5px;padding:6px 8px;background:#ff9800;color:#fff;border-radius:5px;text-decoration:none'>" +
                    "<i class='fa fa-edit'></i> Edit Data</a>" +

                    "<br><br>" +

                    "<a href='/pertanian/create/" +
                    encodeURIComponent(feature.properties.kecamatan) +
                    "' style='display:inline-block;padding:6px 8px;background:#28a745;color:#fff;border-radius:5px;text-decoration:none'>" +
                    "➕ Create Tahun Baru</a>"
                     +

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
        <b>Kecamatan:</b> ${feature.properties.kecamatan}
        <hr>

        <b>Tahun:</b> ${feature.properties.tahun}

        <hr>

        <b>🌾 Padi</b><br>
        Luas Panen : ${feature.properties.padi_luas_panen}<br>
        Produksi : ${feature.properties.padi_produksi}<br>
        Produktivitas : ${feature.properties.padi_produktivitas}

        <hr>

        <b>🌽 Jagung</b><br>
        Luas Panen : ${feature.properties.jagung_luas_panen}<br>
        Produksi : ${feature.properties.jagung_produksi}<br>
        Produktivitas : ${feature.properties.jagung_produktivitas}

        <hr>

        <b>🫘 Kedelai</b><br>
        Luas Panen : ${feature.properties.kedelai_luas_panen}<br>
        Produksi : ${feature.properties.kedelai_produksi}<br>
        Produktivitas : ${feature.properties.kedelai_produktivitas}
        <hr>

        <a href='/map-edit-pertanian/${encodeURIComponent(feature.properties.kecamatan)}'
        style='display:inline-block;margin-top:5px;padding:6px 8px;background:#ff9800;color:#fff;border-radius:5px;text-decoration:none'>
        ✏️ Edit Data</a>

        <br><br>

        <a href='/pertanian/create/${encodeURIComponent(feature.properties.kecamatan)}'
        style='display:inline-block;padding:6px 8px;background:#28a745;color:#fff;border-radius:5px;text-decoration:none'>
        ➕ Create Tahun Baru</a>

        <br><br>

    <!-- 🔥 DELETE BUTTON -->
    <button onclick="hapusData('${feature.properties.tahun}', '${feature.properties.kecamatan}')"
        style="background:#e74c3c;color:#fff;border:none;padding:6px 10px;border-radius:5px;cursor:pointer">
        🗑️ Hapus Data
    </button>


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
    width:110px;
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
    width:110px;
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
    width:110px;
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
        .attr('src','')
        .hide();
}

        function previewImage(event){

    let file = event.target.files[0];

    if(!file) return;

    let preview =
        document.getElementById(
            'preview-image'
        );

    preview.src =
        URL.createObjectURL(file);

    preview.style.display =
        'block';
}

function hapusLahan(id){

    if (!window._confirmDelete) {
    window._confirmDelete = function(msg) {
        return new Promise(resolve => {
            let ok = confirm(msg); // sementara tetap confirm
            resolve(ok);
        });
    }
}
    fetch('/lahan-pertanian/' + id, {
        method:'DELETE',
        headers:{
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
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

    if (!confirm("Yakin hapus data ini?")) return;

    fetch(`/tabel/${tahun}/${encodeURIComponent(kecamatan)}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    })
    .then(res => res.json())
    .then(res => {
        alert("Data berhasil dihapus");
        location.reload();
    })
    .catch(err => {
        console.error(err);
        alert("Gagal menghapus data");
    });
}
    </script>
@endsection
