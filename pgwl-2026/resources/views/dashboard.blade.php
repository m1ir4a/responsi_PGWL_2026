@extends('layouts.template')

@section('styles')
<style>
    .dash-hero {
        background: linear-gradient(135deg, #0d1f14 0%, #1a3d22 100%);
        padding: 2.5rem 2rem 2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .dash-hero::after {
        content: '';
        position: absolute;
        right: -80px; top: -80px;
        width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(26,125,59,.35) 0%, transparent 70%);
        pointer-events: none;
    }

    .dash-hero h1 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: .35rem;
    }

    .dash-hero p {
        color: rgba(255,255,255,.6);
        font-size: .9rem;
        margin: 0;
    }

    .dash-body {
        padding: 1.75rem 2rem;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #c8dece;
        border-radius: 12px;
        padding: 1.25rem 1.4rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: box-shadow .15s;
    }

    .stat-card:hover { box-shadow: 0 4px 16px rgba(26,125,59,.1); }

    .stat-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .stat-icon.green  { background: #e8f5e9; color: #1a7d3b; }
    .stat-icon.amber  { background: #fff8e1; color: #f59e0b; }
    .stat-icon.blue   { background: #e3f2fd; color: #1565c0; }
    .stat-icon.purple { background: #f3e5f5; color: #7b1fa2; }

    .stat-val {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.5rem;
        font-weight: 800;
        color: #0d1f14;
        line-height: 1;
    }

    .stat-lbl {
        font-size: .78rem;
        color: #6b7c70;
        margin-top: .2rem;
    }

    .section-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: #0d1f14;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #c8dece;
    }

    .chart-wrap {
        background: #fff;
        border: 1px solid #c8dece;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .quick-card {
        background: #fff;
        border: 1px solid #c8dece;
        border-radius: 12px;
        padding: 1.25rem;
        text-decoration: none;
        color: inherit;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all .15s;
    }

    .quick-card:hover {
        border-color: #1a7d3b;
        box-shadow: 0 4px 16px rgba(26,125,59,.12);
        transform: translateY(-2px);
        color: inherit;
    }

    .quick-card .arrow {
        margin-left: auto;
        color: #c8dece;
        font-size: .85rem;
        transition: color .15s;
    }

    .quick-card:hover .arrow { color: #1a7d3b; }

    .year-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: #e8f5e9;
        color: #1a7d3b;
        font-size: .78rem;
        font-weight: 600;
        padding: .25rem .7rem;
        border-radius: 100px;
        border: 1px solid #a7d9b6;
    }

    .chart-container {
    position: relative;
    height: 350px; /* sesuaikan */
    width: 100%;
}
</style>
@endsection

@section('content')
<div class="dash-hero">
    <div style="position:relative;z-index:1">
        <h1>🌾 Grobogan AgroMap</h1>
        <p>Grobogan AgroMap menyajikan informasi spasial dan statistik pertanian Kabupaten Grobogan dalam bentuk peta interaktif dan grafik. Sistem ini menampilkan data luas panen, produksi, serta produktivitas komoditas utama seperti padi, jagung, dan kedelai pada tingkat kecamatan untuk mendukung analisis dan pengelolaan sektor pertanian.
    </div>
</div>

<div class="dash-body">

    {{-- STAT CARDS --}}
    <div class="stat-grid">

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fa-solid fa-map-location-dot"></i>
        </div>
        <div>
            <div class="stat-val">{{ $totalKecamatan ?? '19' }}</div>
            <div class="stat-lbl">Kecamatan</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">
            <i class="fa-solid fa-seedling"></i>
        </div>
        <div>
            <div class="stat-val">3</div>
            <div class="stat-lbl">Komoditas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <div>
            <div class="stat-val">
                {{ number_format($totalLuas ?? 0, 2) }}
            </div>
            <div class="stat-lbl">Total Luas Lahan (Ha)</div>
        </div>
    </div>

</div>
    {{-- CHARTS --}}
    <div class="chart-wrap">
        <div class="section-title"><i class="fa-solid fa-chart-bar" style="color:#1a7d3b"></i> Produksi Per Tahun</div>

        <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem" id="tahunBtnGroup">
            @foreach($tahunList ?? [] as $t)
                <button class="year-badge tahun-btn" data-tahun="{{ $t }}" onclick="loadChartDashboard({{ $t }}, this)">
                    <i class="fa-solid fa-calendar-check"></i> {{ $t }}
                </button>
            @endforeach
        </div>

        <div class="chart-container">
    <canvas id="dashChart"></canvas>
</div>
        <p id="chartEmpty" class="text-center text-muted mt-3" style="font-size:.85rem;display:none">
            Pilih tahun di atas untuk menampilkan grafik
        </p>
    </div>

    {{-- QUICK ACCESS --}}
    <div class="section-title"><i class="fa-solid fa-bolt" style="color:#1a7d3b"></i> Akses Cepat</div>
    <div class="quick-links">
        <a href="{{ route('peta') }}" class="quick-card">
            <div class="stat-icon green" style="width:40px;height:40px;font-size:1rem"><i class="fa-solid fa-map"></i></div>
            <div>
                <div style="font-weight:600;font-size:.9rem">Buka Peta</div>
                <div style="font-size:.77rem;color:#6b7c70">Visualisasi spasial kecamatan</div>
            </div>
            <span class="arrow"><i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a href="{{ route('tabel') }}" class="quick-card">
            <div class="stat-icon blue" style="width:40px;height:40px;font-size:1rem"><i class="fa-solid fa-table"></i></div>
            <div>
                <div style="font-weight:600;font-size:.9rem">Tabel Data</div>
                <div style="font-size:.77rem;color:#6b7c70">Data produksi pertanian</div>
            </div>
            <span class="arrow"><i class="fa-solid fa-arrow-right"></i></span>
        </a>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
let dashChartObj = null;

function loadChartDashboard(tahun, btn) {

    // reset highlight button
    document.querySelectorAll('.tahun-btn').forEach(b => {
        b.style.background = '';
        b.style.color = '#1a7d3b';
    });

    if (btn) {
        btn.style.background = '#1a7d3b';
        btn.style.color = '#fff';
    }

    fetch('/geojson-kecamatan-tahun/' + tahun)
        .then(r => r.json())
        .then(data => {

            const features = data.features || [];

            if (features.length === 0) {
                document.getElementById('chartEmpty').style.display = 'block';
                return;
            }

            document.getElementById('chartEmpty').style.display = 'none';

            const labels = features.map(f => f.properties.kecamatan);

            const padi = features.map(f =>
                Number(f.properties.padi_produksi ?? 0)
            );

            const jagung = features.map(f =>
                Number(f.properties.jagung_produksi ?? 0)
            );

            const kedelai = features.map(f =>
                Number(f.properties.kedelai_produksi ?? 0)
            );

            const ctx = document.getElementById('dashChart');

            if (dashChartObj) dashChartObj.destroy();

            dashChartObj = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '🌾 Padi',
                            data: padi,
                            backgroundColor: '#1a7d3b'
                        },
                        {
                            label: '🌽 Jagung',
                            data: jagung,
                            backgroundColor: '#f59e0b'
                        },
                        {
                            label: '🫘 Kedelai',
                            data: kedelai,
                            backgroundColor: '#1565c0'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#eee'
                            }
                        }
                    }
                }
            });
        });
}


// AUTO LOAD TAHUN TERBARU
@if(count($tahunList ?? []) > 0)
window.addEventListener('DOMContentLoaded', function () {
    const btns = document.querySelectorAll('.tahun-btn');

    if (btns.length > 0) {
        const lastBtn = btns[btns.length - 1] || btns[0]; // karena sudah desc di controller
        loadChartDashboard(lastBtn.dataset.tahun, lastBtn);
    }
});
@else
document.getElementById('chartEmpty').style.display = 'block';
@endif

</script>
@endsection
