<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AbsenModel;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class AbsenController extends Controller
{
    public function getAllData()
    {
        $data = AbsenModel::with(['users' => function ($query) {
            $query->select('id', 'uuid', 'name', 'email', 'id_divisi');
        }, 'users.divisi:id,nama_divisi'])->get();
    
        if ($data->isEmpty()) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found',
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'success get all data',
                'data' => $data,
            ]);
        }
    }

    public function createAbsen()
    {
        $tanggalSekarang = now()->toDateString();
        $absensiHariIni = AbsenModel::where('tanggal', $tanggalSekarang)->first();

        if ($absensiHariIni) {
            return response()->json([
                'message' => 'Anda sudah melakukan absen hari ini.'
            ]);
        }
        $jamAbsen = now()->toTimeString();
        if (strtotime($jamAbsen) > strtotime('12:00:00')) {
            $status = 'tidak hadir';
        } elseif (strtotime($jamAbsen) > strtotime('08:00:00')) {
            $status = 'lambat';
        } else {
            $status = 'hadir';
        }

        $user = Auth::user();
        $absensi = new AbsenModel;
        $absensi->uuid = Uuid::uuid4()->toString();
        $absensi->id_user = $user->id; 
        $absensi->tanggal = $tanggalSekarang;
        $absensi->waktu = now()->toTimeString();
        $absensi->status = $status;
        $absensi->save();

        return response()->json([
            'code' => 200,
            'message' => 'Absen berhasil disimpan.', 
            'data' => $absensi
        ]);
    }
}