@extends('layouts.template')

@section('styles')

<link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css">

<style>
body {
    margin: 0;
    padding: 0;
    background: #f4f6f9;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.card-header {
    font-weight: bold;
}

.table thead {
    background: #2e7d32;
    color: white;
}

.table td, .table th {
    font-size: 13px;
    vertical-align: middle;
    text-align: center;
}

h3 {
    margin: 0;
    font-size: 18px;
}
</style>

@endsection


@section('content')

<div class="container mt-4">

    <div class="card">
        <div class="card-header bg-success text-white">
            <h3>Data Produksi Pertanian Per Kecamatan</h3>
        </div>

        <div class="card-body">

            <div style="margin-bottom:10px;">
    <label><b>Pilih Tahun:</b></label>
    <select id="filterTahun" class="form-select" style="width:200px;display:inline-block;">
        <option value="">Semua Tahun</option>

        @foreach($tahunList as $tahun)
            <option value="{{ $tahun }}">{{ $tahun }}</option>
        @endforeach
    </select>
</div>

            <div class="table-responsive">

                <table class="table table-bordered table-striped" id="tabelProduksi">

                    <thead>
                        <tr>
                            <th>Kode Kecamatan</th>
                            <th>Kecamatan</th>

                            <th>Padi Luas Panen</th>
                            <th>Padi Produksi</th>
                            <th>Padi Produktivitas</th>

                            <th>Jagung Luas Panen</th>
                            <th>Jagung Produksi</th>
                            <th>Jagung Produktivitas</th>

                            <th>Kedelai Luas Panen</th>
                            <th>Kedelai Produksi</th>
                            <th>Kedelai Produktivitas</th>

                            <th>Tahun</th>

                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($data as $row)

                        <tr>
                            <td>{{ $row->kode_kec }}</td>
                            <td>{{ $row->kecamatan }}</td>

                            <td>{{ $row->padi_luas_panen }}</td>
                            <td>{{ $row->padi_produksi }}</td>
                            <td>{{ $row->padi_produktivitas }}</td>

                            <td>{{ $row->jagug_luas_panen ?? $row->jagung_luas_panen }}</td>
                            <td>{{ $row->jagung_produksi }}</td>
                            <td>{{ $row->jagung_produktivitas }}</td>

                            <td>{{ $row->kedelai_luas_panen }}</td>
                            <td>{{ $row->kedelai_produksi }}</td>
                            <td>{{ $row->kedelai_produktivitas }}</td>
                            <td>{{ $row->tahun }}</td>


                            <td>
    <button class="btn btn-danger btn-sm"
        onclick="hapusData('{{ $row->tahun }}','{{ $row->kecamatan }}')">
        Hapus
    </button>
</td>
                        </tr>

                        @empty

                        <tr>
                            <td colspan="11">Data tidak tersedia</td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

<div class="container mt-4">

    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h3>Data Lahan Pertanian (Hasil Digitasi)</h3>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped" id="tabelLahan">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Pemilik</th>
                            <th>Kecamatan</th>
                            <th>Komoditas</th>
                            <th>Luas Lahan (Ha)</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($lahan as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->nama_pemilik }}</td>
                                <td>{{ $row->kecamatan }}</td>
                                <td>{{ $row->komoditas }}</td>
                                <td>{{ $row->luas_lahan }}</td>

                                <td>
                                    @if($row->image)
                                        <img src="{{ asset('storage/images/'.$row->image) }}"
                                             width="60"
                                             style="border-radius:6px;">
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <button class="btn btn-danger btn-sm"
                                        onclick="hapusLahan({{ $row->id }})">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">Tidak ada data lahan</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

@endsection


@section('scripts')

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

@section('scripts')

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

<script>
let table = new DataTable('#tabelProduksi', {
    pageLength: 10,
    scrollX: true
});

function hapusData(tahun, kecamatan) {

    if (!confirm("Hapus data kecamatan ini?")) return;

    fetch(`/api/tabel/${tahun}/${kecamatan}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    })
    .then(res => res.json())
    .then(res => {

        alert("Data berhasil dihapus");

        location.reload();

        checkLayer(tahun);
    });
}

function checkLayer(tahun) {

    fetch('/api/layer-check/' + tahun)
        .then(res => res.json())
        .then(res => {

            if (res.count === 0) {
                if (window.layerMap) {
                    map.removeLayer(window.layerMap);
                }
                console.log("Layer hilang karena data kosong");
            }

        });
}

/* =======================
   🔽 TAMBAHKAN INI DI BAWAHNYA
   ======================= */

// FILTER TAHUN
document.addEventListener('DOMContentLoaded', function () {

    const filter = document.getElementById('filterTahun');

    if (!filter) return;

    filter.addEventListener('change', function () {

        let tahun = this.value;

        if (tahun === "") {
            table.column(11).search('').draw();
        } else {
            table.column(11).search(tahun).draw();
        }
    });
});

</script>


@endsection
