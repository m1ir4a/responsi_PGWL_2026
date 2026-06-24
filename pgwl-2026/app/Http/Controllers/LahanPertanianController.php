<?php

namespace App\Http\Controllers;

use App\Models\LahanPertanian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LahanPertanianController extends Controller
{
    public function index()
{
    $data = LahanPertanian::selectRaw("
    id,
    nama_pemilik,
    kecamatan,
    komoditas,
    luas_lahan,
    image,
    ST_AsGeoJSON(geom) as geom_json
")->get();

    return response()->json([
        "type" => "FeatureCollection",
        "features" => $data->map(function ($item) {
            return [
                "type" => "Feature",
                "geometry" => json_decode($item->geom_json),
                "properties" => [
                    "id" => $item->id,
                    "nama_pemilik" => $item->nama_pemilik,
                    "kecamatan" => $item->kecamatan,
                    "komoditas" => $item->komoditas,
                    "luas_lahan" => $item->luas_lahan,
                    "image" => $item->image
                ]
            ];
        })
    ]);
}

public function store(Request $request)
{
    $image = null;

    if ($request->hasFile('image')) {

        $file = $request->file('image');

        $image =
            time() . '_' .
            $file->getClientOriginalName();

        $file->storeAs(
            'images',
            $image,
            'public'
        );
    }

    $data = new LahanPertanian();

    $data->nama_pemilik = $request->nama_pemilik;
    $data->kecamatan = $request->kecamatan;
    $data->komoditas = $request->komoditas;
    $data->luas_lahan = $request->luas_lahan;

    // GeoJSON
    $data->geom = $request->geom;

    $data->image = $image;

    $data->save();

    return response()->json([
        'status' => 'success'
    ]);
}

public function update(Request $request, $id)
{



   $lahan = LahanPertanian::selectRaw("
    *,
    ST_AsGeoJSON(geom) as geom_json
")
->findOrFail($id);

    $lahan->nama_pemilik = $request->nama_pemilik;
    $lahan->kecamatan = $request->kecamatan;
    $lahan->komoditas = $request->komoditas;
    $lahan->luas_lahan = $request->luas_lahan;

    // GeoJSON
    $lahan->geom = $request->geometry;

    if ($request->hasFile('image')) {

        if (
            $lahan->image &&
            Storage::disk('public')->exists(
                'images/' . $lahan->image
            )
        ) {
            Storage::disk('public')->delete(
                'images/' . $lahan->image
            );
        }

        $file = $request->file('image');

        $filename =
            time() . '_' .
            $file->getClientOriginalName();

        $file->storeAs(
            'images',
            $filename,
            'public'
        );

        $lahan->image = $filename;
    }

    $lahan->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Data lahan berhasil diperbarui'
    ]);
}


public function editMap($id)
{
    $lahan = LahanPertanian::findOrFail($id);

    return view(
        'map-edit-lahan',
        compact('id', 'lahan')
    );
}

public function geojson($id)
{
    $lahan = LahanPertanian::findOrFail($id);

    return response()->json([
        "type" => "FeatureCollection",
        "features" => [
            [
                "type" => "Feature",

                "geometry" => json_decode($lahan->geom),

                "properties" => [
                    "id" => $lahan->id,
                    "nama_pemilik" => $lahan->nama_pemilik,
                    "kecamatan" => $lahan->kecamatan,
                    "komoditas" => $lahan->komoditas,
                    "luas_lahan" => $lahan->luas_lahan,
                    "image" => $lahan->image
                ]
            ]
        ]
    ]);
}

public function destroy($id)
{
    $lahan =
        LahanPertanian::findOrFail($id);

    if(
        $lahan->image &&
        Storage::disk('public')->exists(
            'images/' . $lahan->image
        )
    ){
        Storage::disk('public')->delete(
            'images/' . $lahan->image
        );
    }

    $lahan->delete();

    return response()->json([
        'status' => 'success'
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

    // 🔥 TAMBAHKAN INI
    $lahan = DB::table('lahan_pertanian')->get();

    return view('table', compact('data', 'tahunList', 'lahan'));
}

}
