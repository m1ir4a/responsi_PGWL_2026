@extends('layouts.template')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css">
<style>
    body {
        margin: 0;
        padding: 0;
    }

    .table-responsive {
        font-size: 0.92rem;
    }
</style>
@endsection

@section('content')
<div class="container mt-4 mb-5">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h3 class="mb-0">Tabel Data Produksi Pertanian</h3>

        <form method="GET" action="{{ route('tabel') }}" class="d-flex gap-2">
            <select name="tahun" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Tahun</option>
                @foreach ($tahunList as $tahun)
                    <option value="{{ $tahun }}" {{ (string) $tahunDipilih === (string) $tahun ? 'selected' : '' }}>
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white">
            <i class="fa-solid fa-seedling"></i> Data Pertanian per Kecamatan
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle" id="tabelPertanian">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle">Kecamatan</th>
                            <th rowspan="2" class="align-middle">Tahun</th>
                            <th colspan="3" class="text-center">🌾 Padi</th>
                            <th colspan="3" class="text-center">🌽 Jagung</th>
                            <th colspan="3" class="text-center">🫘 Kedelai</th>
                            <th rowspan="2" class="align-middle">Aksi</th>
                        </tr>
                        <tr>
                            <th>Luas Panen (Ha)</th>
                            <th>Produksi (Ton)</th>
                            <th>Produktivitas (Ton/Ha)</th>

                            <th>Luas Panen (Ha)</th>
                            <th>Produksi (Ton)</th>
                            <th>Produktivitas (Ton/Ha)</th>

                            <th>Luas Panen (Ha)</th>
                            <th>Produksi (Ton)</th>
                            <th>Produktivitas (Ton/Ha)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $no = 1; @endphp
                        @forelse ($data as $row)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $row->kecamatan }}</td>
                                <td>{{ $row->tahun }}</td>

                                <td>{{ $row->padi_luas_panen }}</td>
                                <td>{{ $row->padi_produksi }}</td>
                                <td>{{ $row->padi_produktivitas }}</td>

                                <td>{{ $row->jagung_luas_panen }}</td>
                                <td>{{ $row->jagung_produksi }}</td>
                                <td>{{ $row->jagung_produktivitas }}</td>

                                <td>{{ $row->kedelai_luas_panen }}</td>
                                <td>{{ $row->kedelai_produksi }}</td>
                                <td>{{ $row->kedelai_produktivitas }}</td>

                                <td class="text-nowrap">
                                    @auth
                                        <a href="{{ route('pertanian.edit', $row->kecamatan) }}"
                                           class="btn btn-sm btn-warning">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-lock"></i> Login untuk Edit
                                        </a>
                                    @endauth
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted py-4">
                                    Belum ada data pertanian
                                    @if ($tahunDipilih)
                                        untuk tahun {{ $tahunDipilih }}
                                    @endif
                                    . Tambahkan data melalui halaman
                                    <a href="{{ route('peta') }}">Peta</a>.
                                </td>
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
<script>
    new DataTable('#tabelPertanian', {
        paging: true,
        order: [[1, 'asc']],
    });
</script>
@endsection
