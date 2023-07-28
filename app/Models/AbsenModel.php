<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenModel extends Model
{
    use HasFactory;
    protected $table = 'tb_absen';
    protected $fillable = [
        'id' , 'uuid' , 'id_user' ,'tanggal', 'waktu' , 'status' , 'created_at' , 'updated_at'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getTahun($id_user)
    {
        $data = $this->join('users', 'users.id_user', '=', 'users.id')
            ->select('users.uuid', 'users.name','email' , 'id_divisi')
            ->where('users.id_user', '=', $id_user)
            ->first();
        return $data;
    }
}
