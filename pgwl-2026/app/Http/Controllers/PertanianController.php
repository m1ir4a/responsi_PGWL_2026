<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PertanianController extends Controller
{
    public function edit($tahun, $nama)
{
    $data = DB::table('produksi_pertanian')
        ->where('tahun', $tahun)
        ->whereRaw(
            'LOWER(TRIM(kecamatan)) = LOWER(TRIM(?))',
            [$nama]
        )
        ->first();

    if (!$data) {
        abort(404,'Data tidak ditemukan');
    }

    return view('map-edit-pertanian', [
        'data' => $data,
        'title' => 'Edit Pertanian'
    ]);
}

    public function update(Request $request, $tahun, $nama)
{
    DB::table('produksi_pertanian')
        ->where('tahun', $tahun)
        ->where('kecamatan', $nama)
        ->update([

            'padi_luas_panen' => $request->padi_luas_panen,
            'padi_produksi' => $request->padi_produksi,
            'padi_produktivitas' => $request->padi_produktivitas,

            'jagung_luas_panen' => $request->jagung_luas_panen,
            'jagung_produksi' => $request->jagung_produksi,
            'jagung_produktivitas' => $request->jagung_produktivitas,

            'kedelai_luas_panen' => $request->kedelai_luas_panen,
            'kedelai_produksi' => $request->kedelai_produksi,
            'kedelai_produktivitas' => $request->kedelai_produktivitas,
        ]);

    return redirect()
        ->route('peta')
        ->with('success', 'Data berhasil diedit');
}
}
