<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LahanPertanian extends Model
{
    protected $table = 'lahan_pertanian';

    public $timestamps = false;

    protected $fillable = [
        'nama_pemilik',
        'kecamatan',
        'komoditas',
        'luas_lahan',
        'geom',
        'image'
    ];
}
