<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisiModel extends Model
{
    use HasFactory;
    protected $table = 'tb_divisi';
    protected $fillable = [
        'id' , 'uuid' , 'nama_divisi' , 'created_at' , 'updated_at'
    ];
}
