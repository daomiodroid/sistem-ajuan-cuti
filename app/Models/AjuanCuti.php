<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AjuanCuti extends Model
{
    protected $table = 'ajuan_cuti';

    protected $fillable = [
        'nip',
        'nama',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
}
