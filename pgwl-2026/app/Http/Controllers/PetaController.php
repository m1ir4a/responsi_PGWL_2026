<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PetaController extends Controller
{
    public function peta()
    {
        return view('peta');
    }

    public function geojsonKecamatan()
    {
        $data = DB::select("
            SELECT
                a.namobj,
                p.padi_luas_panen,
                p.padi_produksi,
                p.jagung_produksi,
                p.kedelai_produksi,
                ST_AsGeoJSON(a.geom) as geometry
            FROM adm_grobogan a
            LEFT JOIN produksi_pertanian p
            ON UPPER(a.namobj) = UPPER(p.kecamatan)
        ");

        $features = [];

        foreach ($data as $row) {
            $features[] = [
                "type" => "Feature",
                "geometry" => json_decode($row->geometry),
                "properties" => [
                    "kecamatan" => $row->namobj,
                    "padi_luas_panen" => $row->padi_luas_panen,
                    "padi_produksi" => $row->padi_produksi,
                    "jagung_produksi" => $row->jagung_produksi,
                    "kedelai_produksi" => $row->kedelai_produksi,
                ]
            ];
        }

        return response()->json([
            "type" => "FeatureCollection",
            "features" => $features
        ]);
    }
}