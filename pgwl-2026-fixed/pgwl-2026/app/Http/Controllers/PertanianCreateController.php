<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PertanianCreateController extends Controller
{
    public function create($kecamatan)
{
    return view('create', [
        'title' => 'Tambah Data Pertanian',
        'kecamatan' => $kecamatan
    ]);
}

    /**
     * Simpan data pertanian baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|numeric',

            'kecamatan' => 'required|string',

            'padi_luas_panen' => 'nullable|numeric',
            'padi_produksi' => 'nullable|numeric',
            'padi_produktivitas' => 'nullable|numeric',

            'jagung_luas_panen' => 'nullable|numeric',
            'jagung_produksi' => 'nullable|numeric',
            'jagung_produktivitas' => 'nullable|numeric',

            'kedelai_luas_panen' => 'nullable|numeric',
            'kedelai_produksi' => 'nullable|numeric',
            'kedelai_produktivitas' => 'nullable|numeric',
        ]);

        /*
        Cek apakah kecamatan + tahun
        sudah ada
        */
        $cek = DB::table('produksi_pertanian')
            ->where('kecamatan', $request->kecamatan)
            ->where('tahun', $request->tahun)
            ->first();

        if ($cek) {
            return back()->with(
                'error',
                'Data kecamatan dan tahun tersebut sudah ada.'
            );
        }

        DB::table('produksi_pertanian')->insert([
            'tahun' => $request->tahun,
            'kecamatan' => $request->kecamatan,

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
            ->with('success', 'Data pertanian berhasil ditambahkan.');
    }
}
