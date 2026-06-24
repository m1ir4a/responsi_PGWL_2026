@extends('layouts.template')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">

<style>
    html,body{
        height:100%;
        margin:0;
        padding:0;
    }

    #map{
        height:calc(100vh - 56px);
    }

    #modalEdit{
    z-index:9999 !important;
}

.modal-backdrop{
    z-index:9998 !important;
}

#modalEdit .modal-dialog{
    margin-top:80px;
}
</style>
@endsection

@section('content')

<div id="map"></div>

<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Edit Lahan Pertanian
                </h5>
            </div>

            <form id="formEdit">

                <input type="hidden" id="id">

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Nama Pemilik</label>
                        <input type="text"
                            class="form-control"
                            id="nama_pemilik">
                    </div>

                    <div class="mb-3">
                        <label>Kecamatan</label>
                        <input type="text"
                            class="form-control"
                            id="kecamatan">
                    </div>

                    <div class="mb-3">
                        <label>Komoditas</label>
                        <input type="text"
                            class="form-control"
                            id="komoditas">
                    </div>

                    <div class="mb-3">
                        <label>Luas Lahan (Ha)</label>
                        <input type="number"
                            class="form-control"
                            id="luas_lahan">
                    </div>

                    <div class="mb-3">
                        <label>Geometry</label>
                        <textarea
                            id="geometry"
                            class="form-control"
                            rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Gambar</label>
                        <input type="file"
                            class="form-control"
                            id="image">
                    </div>

                    <img id="preview-image"
                        class="img-thumbnail"
                        width="250">

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="button"
                        class="btn btn-primary"
                        onclick="updateLahan()">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@section('scripts')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>

var map = L.map('map').setView([-7.1,110.9],10);

L.tileLayer(
'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
{
    attribution:'OpenStreetMap'
}
).addTo(map);

var drawnItems = new L.FeatureGroup();

map.addLayer(drawnItems);

var drawControl =
new L.Control.Draw({

    draw:false,

    edit:{
        featureGroup:drawnItems,
        edit:true,
        remove:false
    }

});

map.addControl(drawControl);

var selectedId = null;

fetch('/lahan-pertanian')
.then(res=>res.json())
.then(data=>{

    var lahanLayer = L.geoJSON(data,{

        style:{
            color:'red',
            weight:2,
            fillOpacity:0.4
        },

        onEachFeature:function(feature,layer){

            layer.feature = feature;

            drawnItems.addLayer(layer);

            layer.on('click',function(){

                selectedId =
                feature.properties.id;

                $('#id').val(
                    feature.properties.id
                );

                $('#nama_pemilik').val(
                    feature.properties.nama_pemilik
                );

                $('#kecamatan').val(
                    feature.properties.kecamatan
                );

                $('#komoditas').val(
                    feature.properties.komoditas
                );

                $('#luas_lahan').val(
                    feature.properties.luas_lahan
                );

                $('#geometry').val(
                    JSON.stringify(
                        feature.geometry
                    )
                );

                if(feature.properties.image){

                    $('#preview-image').attr(
                        'src',
                        '/storage/images/' +
                        feature.properties.image
                    );

                }

                $('#modalEdit').modal('show');

            });

        }

    });

    map.fitBounds(
        lahanLayer.getBounds()
    );

});

map.on('draw:edited', function(e){

    let layer = null;

    e.layers.eachLayer(function(l){
        layer = l;
    });

    if(!layer) return;

    let geojson = layer.toGeoJSON();

    $('#geometry').val(JSON.stringify(geojson.geometry));

    selectedLayer = layer;

    selectedId = layer.feature?.properties?.id || selectedId;

    $('#id').val(layer.feature?.properties?.id || '');
    $('#nama_pemilik').val(layer.feature?.properties?.nama_pemilik || '');
    $('#kecamatan').val(layer.feature?.properties?.kecamatan || '');
    $('#komoditas').val(layer.feature?.properties?.komoditas || '');
    $('#luas_lahan').val(layer.feature?.properties?.luas_lahan || '');

    if(layer.feature?.properties?.image){
        $('#preview-image').attr(
            'src',
            '/storage/images/' + layer.feature.properties.image
        );
    }

    setTimeout(() => {
        $('#modalEdit').modal('show');
    }, 150);

});

function updateLahan(){

    let formData =
    new FormData();

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
        'geometry',
        $('#geometry').val()
    );

    let image =
    $('#image')[0].files[0];

    if(image){
        formData.append(
            'image',
            image
        );
    }

    fetch(
        '/lahan-pertanian/' +
        selectedId,
        {
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':
                '{{ csrf_token() }}'
            },
            body:formData
        }
    )
    .then(res=>res.json())
    .then(res=>{

    const toast = document.createElement('div');

    toast.innerHTML =
        'Data berhasil diupdate';

    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '99999';
    toast.style.background = '#28a745';
    toast.style.color = '#fff';
    toast.style.padding = '12px 20px';
    toast.style.borderRadius = '8px';
    toast.style.boxShadow = '0 2px 10px rgba(0,0,0,.2)';

    document.body.appendChild(toast);

    setTimeout(function(){

        window.location.href = '/peta';

    },1500);

});

}

</script>

@endsection
