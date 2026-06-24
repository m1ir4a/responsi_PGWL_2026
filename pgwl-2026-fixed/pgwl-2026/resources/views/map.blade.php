@extends('layouts.template')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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
    </style>
@endsection

@section('content')
    <div id="map"></div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        /*  MAP INIT */
        var map = L.map('map').setView([-7.1, 110.9], 10);

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
                v > 4 ? '#f16913' :
                v > 2 ? '#fd8d3c' :
                v > 0 ? '#fdbe85' :
                '#feedde';
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
            style: function() {
                return {
                    color: "#000",
                    weight: 1,
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


        function popupPertanian(feature){

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


    `;
}

        var tahunLayers = {};

        fetch('/tahun-pertanian')
.then(res => res.json())
.then(function(tahunList){

    tahunList.forEach(function(tahun){

        tahunLayers[tahun] = {

    kecamatan: L.geoJSON(null,{
    style:{
        color:"#000",
        weight:1,
        fillColor:"#4CAF50",
        fillOpacity:0.5
    },

    onEachFeature:function(feature,layer){

        layer.bindPopup(
            popupPertanian(feature)
        );

        layer.bindTooltip(
            feature.properties.kecamatan,
            {
                sticky:true,
                direction:"top",
                className:"my-tooltip"
            }
        );
    }
}),

    padi: L.geoJSON(null,{
        style:function(feature){
            return{
                color:"#555",
                weight:1,
                fillColor:getColorPadi(
                    safe(feature.properties.padi_produktivitas)
                ),
                fillOpacity:0.75
            };
        },

        onEachFeature:function(feature,layer){

        layer.bindPopup(
            popupPertanian(feature)
        );

        layer.bindTooltip(
            feature.properties.kecamatan,
            {
                sticky:true,
                direction:"top",
                className:"my-tooltip"
            }
        );
    }
    }),

    jagung: L.geoJSON(null,{
        style:function(feature){
            return{
                color:"#555",
                weight:1,
                fillColor:getColorJagung(
                    safe(feature.properties.jagung_produktivitas)
                ),
                fillOpacity:0.75
            };
        },

        onEachFeature:function(feature,layer){

        layer.bindPopup(
            popupPertanian(feature)
        );

        layer.bindTooltip(
            feature.properties.kecamatan,
            {
                sticky:true,
                direction:"top",
                className:"my-tooltip"
            }
        );
    }
    }),

    kedelai: L.geoJSON(null,{
        style:function(feature){
            return{
                color:"#555",
                weight:1,
                fillColor:getColorKedelai(
                    safe(feature.properties.kedelai_produktivitas)
                ),
                fillOpacity:0.75
            };
        },

        onEachFeature:function(feature,layer){

        layer.bindPopup(
            popupPertanian(feature)
        );

        layer.bindTooltip(
            feature.properties.kecamatan,
            {
                sticky:true,
                direction:"top",
                className:"my-tooltip"
            }
        );
    }

    }),

    luas : L.layerGroup(),
    produksi : L.layerGroup(),
    produktivitas : L.layerGroup()
};

$.getJSON('/geojson-kecamatan-tahun/' + tahun, function(data){

    tahunLayers[tahun].kecamatan.clearLayers();
tahunLayers[tahun].padi.clearLayers();
tahunLayers[tahun].jagung.clearLayers();
tahunLayers[tahun].kedelai.clearLayers();


    tahunLayers[tahun].kecamatan.addData(data);

    tahunLayers[tahun].padi.addData(data);

    tahunLayers[tahun].jagung.addData(data);

    tahunLayers[tahun].kedelai.addData(data);

    // =====================
        // ADD DATA BARU
        // =====================
        kecamatan.addData(data);
        choroplethPadi.addData(data);
        choroplethJagung.addData(data);
        choroplethKedelai.addData(data);

        // =====================
        // UPDATE BAR CHART
        // =====================
        rebuildBarChart(data);

        map.fitBounds(kecamatan.getBounds());

});

        overlayMaps["🗺️ Tahun " + tahun + " - Pertanian Kabupaten Grobogan"]
            = tahunLayers[tahun].kecamatan;

        overlayMaps["🌾 Tahun " + tahun + " - Choropleth Padi"]
            = tahunLayers[tahun].padi;

        overlayMaps["🌽 Tahun " + tahun + " - Choropleth Jagung"]
            = tahunLayers[tahun].jagung;

        overlayMaps["🫘 Tahun " + tahun + " - Choropleth Kedelai"]
            = tahunLayers[tahun].kedelai;

        overlayMaps["🟩 Tahun " + tahun + " - Bar Chart Luas Panen"]
            = tahunLayers[tahun].luas;

        overlayMaps["📦 Tahun " + tahun + " - Bar Chart Produksi"]
            = tahunLayers[tahun].produksi;

        overlayMaps["📊 Tahun " + tahun + " - Bar Chart Produktivitas"]
            = tahunLayers[tahun].produktivitas;

    });

    L.control.layers(baseMaps, overlayMaps).addTo(map);

});

        /* =======================
           LOAD GEOJSON
        ======================= */
        $.getJSON("{{ route('geojson.kecamatan') }}", function(data) {

            kecamatan.addData(data);
            kecamatan.addTo(map);
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

        var overlayMaps = {};

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
    </script>
@endsection
