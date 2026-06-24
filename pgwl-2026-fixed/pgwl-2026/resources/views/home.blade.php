@extends('layouts.template')

@section('styles')
<style>
    .hero {
        background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 45%, #558b2f 100%);
        color: #fff;
    }

    .hero h1 {
        font-weight: 700;
    }

    .feature-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: .75rem;
    }

    .stat-card {
        border: none;
        border-radius: 14px;
        background: #f8f9fa;
    }
</style>
@endsection

@section('content')

{{-- HERO --}}
<section class="hero py-5">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-light text-success mb-3">WebGIS Pertanian</span>
                <h1 class="display-5 mb-3">Sistem Informasi Geografis Produksi Pertanian Kabupaten Grobogan</h1>
                <p class="lead mb-4">
                    Pantau luas panen, produksi, dan produktivitas Padi, Jagung, dan Kedelai
                    di setiap kecamatan Kabupaten Grobogan secara interaktif melalui peta digital.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('peta') }}" class="btn btn-light btn-lg text-success fw-semibold">
                        <i class="fa-solid fa-map-location-dot"></i> Lihat Peta
                    </a>
                    <a href="{{ route('tabel') }}" class="btn btn-outline-light btn-lg">
                        <i class="fa-solid fa-table"></i> Lihat Tabel Data
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg">
                            <i class="fa-solid fa-chart-line"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                            <i class="fa-solid fa-right-to-bracket"></i> Login Admin
                        </a>
                    @endauth
                </div>
            </div>

            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card stat-card p-3 text-center text-dark">
                            <div class="fs-2 fw-bold text-success">{{ $jumlahKecamatan }}</div>
                            <div class="small text-muted">Kecamatan Tercatat</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card stat-card p-3 text-center text-dark">
                            <div class="fs-2 fw-bold text-success">{{ $tahunTersedia->count() }}</div>
                            <div class="small text-muted">Tahun Data</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card stat-card p-3 text-center text-dark">
                            <div class="fw-bold text-success mb-1">🌾 Padi &nbsp; 🌽 Jagung &nbsp; 🫘 Kedelai</div>
                            <div class="small text-muted">
                                @if ($tahunTersedia->isNotEmpty())
                                    Data tersedia untuk tahun {{ $tahunTersedia->first() }} &ndash; {{ $tahunTersedia->last() }}
                                @else
                                    Belum ada data, silakan tambahkan melalui halaman Peta
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FITUR --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Fitur Utama</h2>
            <p class="text-muted">Kelola dan pantau data pertanian dengan mudah</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="feature-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-map"></i>
                </div>
                <h5>Peta Interaktif</h5>
                <p class="text-muted">Visualisasi choropleth dan grafik batang per kecamatan untuk tiap komoditas dan tahun.</p>
            </div>
            <div class="col-md-3">
                <div class="feature-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-table"></i>
                </div>
                <h5>Tabel Data</h5>
                <p class="text-muted">Lihat dan saring seluruh data produksi pertanian per tahun dalam bentuk tabel.</p>
            </div>
            <div class="col-md-3">
                <div class="feature-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h5>Dashboard Analitik</h5>
                <p class="text-muted">Ringkasan statistik, tren produksi tahunan, dan peringkat kecamatan setelah login.</p>
            </div>
            <div class="col-md-3">
                <div class="feature-icon bg-danger-subtle text-danger">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h5>Akses Aman</h5>
                <p class="text-muted">Penambahan dan perubahan data dilindungi oleh sistem login.</p>
            </div>
        </div>
    </div>
</section>

{{-- TENTANG --}}
<section id="tentang" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3">Tentang PGWL</h2>
                <p>
                    PGWL (Peta GIS Web Lahan) adalah WebGIS yang dikembangkan untuk membantu
                    pemantauan produksi pertanian di Kabupaten Grobogan. Data yang ditampilkan meliputi
                    luas panen, produksi, dan produktivitas untuk tiga komoditas utama: Padi, Jagung, dan Kedelai,
                    yang dikelompokkan per kecamatan dan per tahun.
                </p>
                <p>
                    Data batas administrasi kecamatan ditampilkan dalam bentuk peta choropleth, dilengkapi
                    grafik batang perbandingan antar komoditas, serta fitur tambah dan edit data bagi pengguna yang sudah login.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="mb-3">Mulai Eksplorasi</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('peta') }}" class="btn btn-success">
                            <i class="fa-solid fa-map"></i> Buka Peta Pertanian
                        </a>
                        <a href="{{ route('tabel') }}" class="btn btn-outline-success">
                            <i class="fa-solid fa-table"></i> Buka Tabel Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="bg-dark text-light py-4">
    <div class="container text-center small">
        &copy; {{ date('Y') }} PGWL &mdash; WebGIS Pertanian Kabupaten Grobogan
    </div>
</footer>

@endsection
