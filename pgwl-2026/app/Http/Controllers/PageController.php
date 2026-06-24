<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;


class PageController extends Controller
{
    public function landingpage()
    {
        return view('dashboard', [
            'title' => 'PGWL'
        ]);
    }

    public function peta()
    {
        return view('map', [
            'title' => 'Peta'
        ]);
    }

    public function tabel()
{
    $data = DB::table('produksi_pertanian')->get();

    $tahunList = DB::table('produksi_pertanian')
        ->select('tahun')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    $lahan = DB::table('lahan_pertanian')->get();

    return view('table', [
    'title' => 'Tabel Data',
    'data' => $data,
    'tahunList' => $tahunList,
    'lahan' => $lahan
]);
}

    // =========================
    // GEOJSON KECAMATAN + PRODUKSI
    // =========================
    public function geojsonKecamatan()
{
    $data = DB::select("
        SELECT
            a.\"NAMOBJ\" AS kecamatan,

            p.padi_luas_panen,
            p.padi_produksi,
            p.padi_produktivitas,

            p.jagung_luas_panen,
            p.jagung_produksi,
            p.jagung_produktivitas,

            p.kedelai_luas_panen,
            p.kedelai_produksi,
            p.kedelai_produktivitas,

            ST_AsGeoJSON(a.geom) AS geom

        FROM adm_grobogan1 a
        LEFT JOIN produksi_pertanian p
        ON LOWER(TRIM(a.\"NAMOBJ\")) = LOWER(TRIM(p.kecamatan))
    ");

    $features = [];

    foreach ($data as $row) {
        $features[] = [
            "type" => "Feature",
            "geometry" => json_decode($row->geom),

            "properties" => [
                "kecamatan" => $row->kecamatan,

                "padi_luas_panen" => $row->padi_luas_panen,
                "padi_produksi" => $row->padi_produksi,
                "padi_produktivitas" => $row->padi_produktivitas,

                "jagung_luas_panen" => $row->jagung_luas_panen,
                "jagung_produksi" => $row->jagung_produksi,
                "jagung_produktivitas" => $row->jagung_produktivitas,

                "kedelai_luas_panen" => $row->kedelai_luas_panen,
                "kedelai_produksi" => $row->kedelai_produksi,
                "kedelai_produktivitas" => $row->kedelai_produktivitas,
            ]
        ];
    }

    return response()->json([
        "type" => "FeatureCollection",
        "features" => $features
    ]);
}

public function geojsonKecamatanTahun($tahun)
{
    $data = DB::select("
        SELECT
            a.\"NAMOBJ\" as kecamatan,
            p.tahun,
            p.padi_luas_panen,
            p.padi_produksi,
            p.padi_produktivitas,
            p.jagung_luas_panen,
            p.jagung_produksi,
            p.jagung_produktivitas,
            p.kedelai_luas_panen,
            p.kedelai_produksi,
            p.kedelai_produktivitas,
            ST_AsGeoJSON(a.geom) as geom
        FROM adm_grobogan1 a
        LEFT JOIN produksi_pertanian p
        ON LOWER(TRIM(a.\"NAMOBJ\")) =
           LOWER(TRIM(p.kecamatan))
        AND p.tahun = ?
    ", [$tahun]);

    return response()->json([
        "type" => "FeatureCollection",
        "features" => collect($data)->map(function($r){
            return [
                "type" => "Feature",
                "geometry" => json_decode($r->geom),
                "properties" => [
                    "kecamatan" => $r->kecamatan,
                    "tahun" => $r->tahun,

                    "padi_luas_panen" => $r->padi_luas_panen,
                    "padi_produksi" => $r->padi_produksi,
                    "padi_produktivitas" => $r->padi_produktivitas,

                    "jagung_luas_panen" => $r->jagung_luas_panen,
                    "jagung_produksi" => $r->jagung_produksi,
                    "jagung_produktivitas" => $r->jagung_produktivitas,

                    "kedelai_luas_panen" => $r->kedelai_luas_panen,
                    "kedelai_produksi" => $r->kedelai_produksi,
                    "kedelai_produktivitas" => $r->kedelai_produktivitas,
                ]
            ];
        })->values()
    ]);
}

public function daftarTahun()
{
    $tahun = DB::table('produksi_pertanian')
        ->select('tahun')
        ->distinct()
        ->orderBy('tahun')
        ->pluck('tahun');

    return response()->json($tahun);
}

public function getTahun()
{
    return DB::table('produksi_pertanian')
        ->select('tahun')
        ->distinct()
        ->orderBy('tahun')
        ->pluck('tahun');
}

// =========================
    // API: ALL DATA FOR TABLE
    // =========================
    public function pertanianData()
    {
        $data = DB::table('produksi_pertanian')
            ->orderBy('tahun')
            ->orderBy('kecamatan')
            ->get();

        return response()->json($data);
    }

public function checkLayer($tahun)
{
    $count = DB::table('produksi_pertanian')
        ->where('tahun', $tahun)
        ->count();

    return response()->json([
        'count' => $count
    ]);
}

public function destroy($tahun, $kecamatan)
{
    DB::table('produksi_pertanian')
        ->where('tahun', $tahun)
        ->where('kecamatan', $kecamatan)
        ->delete();

    return response()->json([
        'success' => true,
        'message' => 'Data berhasil dihapus'
    ]);
}

public function dashboard()
{
    // total kecamatan dari produksi
    $totalKecamatan = DB::table('produksi_pertanian')
        ->distinct('kecamatan')
        ->count('kecamatan');

    $totalLuas = DB::table('lahan_pertanian')
    ->select(DB::raw('COALESCE(SUM(luas_lahan),0) as total'))
    ->value('total');

    // tahun untuk chart
    $tahunList = DB::table('produksi_pertanian')
        ->select('tahun')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    return view('dashboard', [
        'title' => 'Grobogan AgroMap',
        'totalKecamatan' => $totalKecamatan,
        'totalLuas' => $totalLuas,
        'tahunList' => $tahunList
    ]);
}





}

