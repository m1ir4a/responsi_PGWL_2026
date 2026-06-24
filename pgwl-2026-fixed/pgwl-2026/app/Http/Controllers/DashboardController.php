<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tahunTerbaru = DB::table('produksi_pertanian')->max('tahun');

        $ringkasanTahunIni = DB::table('produksi_pertanian')
            ->where('tahun', $tahunTerbaru)
            ->selectRaw('
                COUNT(*) as jumlah_kecamatan,
                SUM(padi_produksi) as total_padi,
                SUM(jagung_produksi) as total_jagung,
                SUM(kedelai_produksi) as total_kedelai,
                AVG(padi_produktivitas) as rata_padi,
                AVG(jagung_produktivitas) as rata_jagung,
                AVG(kedelai_produktivitas) as rata_kedelai
            ')
            ->first();

        $trenTahunan = DB::table('produksi_pertanian')
            ->selectRaw('
                tahun,
                SUM(padi_produksi) as padi,
                SUM(jagung_produksi) as jagung,
                SUM(kedelai_produksi) as kedelai
            ')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        $topPadi = DB::table('produksi_pertanian')
            ->where('tahun', $tahunTerbaru)
            ->orderByDesc('padi_produksi')
            ->limit(5)
            ->get(['kecamatan', 'padi_produksi']);

        $dataTerbaru = DB::table('produksi_pertanian')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $jumlahKecamatanTerdaftar = DB::table('produksi_pertanian')
            ->distinct()
            ->count('kecamatan');

        return view('dashboard', [
            'title' => 'Dashboard',
            'tahunTerbaru' => $tahunTerbaru,
            'ringkasan' => $ringkasanTahunIni,
            'trenTahunan' => $trenTahunan,
            'topPadi' => $topPadi,
            'dataTerbaru' => $dataTerbaru,
            'jumlahKecamatan' => $jumlahKecamatanTerdaftar,
        ]);
    }
}
