<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DivisiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class DivisiController extends Controller
{
    public function getAllData()
    {
        $data = DivisiModel::all();
        if ($data->isEmpty()) {
            return response()->json([
                'code' => 404,
                'message' => 'Data tidak ditemukan'
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'Data success di tampilkan',
                'data' => $data
            ]);
        }
    }

    public function createData(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'nama_divisi' => 'required|unique:tb_divisi'
            ],
            [
                'nama_divisi.required' => 'Form nama divisi tidak boleh kosong',
                'nama_divisi.unique' => 'Data sebelumnya sudah ada'
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'check your validation',
                'errors' => $validation->errors()
            ]);
        }

        try {
            $data = new DivisiModel;
            $data->uuid = Uuid::uuid4()->toString();
            $data->nama_divisi = $request->input('nama_divisi');
            $data->save();
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'failed',
                'errors' => $th->getMessage()
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Berhasil menambahkan data ',
            'data' => $data
        ]);
    }

    public function getDataByUuid($uuid)
    {
        if (!Uuid::isValid($uuid)) {
            return response()->json([
                'code' => 404,
                'message' => 'UUID Tidak ditemukan'
            ]);
        }
        try {
            $data = DivisiModel::where('uuid', $uuid)->first();
            if (!$data) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Data tidak ditemukan'
                ]);
            } else {
                return response()->json([
                    'code' => 200,
                    'message' => 'Berhasil mengambil data berdasarkan UUID',
                    'data' => $data
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'failed',
                'errors' => $th->getMessage()
            ]);
        }
    }

    public function updateData(Request $request, $uuid)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'nama_divisi' => 'required|unique:tb_divisi'
            ],
            [
                'nama_divisi.required' => 'Form nama divisi tidak boleh kosong',
                'nama_divisi.unique' => 'Data sebelumnya sudah ada'
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'check your validation',
                'errors' => $validation->errors()
            ]);
        }

        try {
            $data = DivisiModel::where('uuid', $uuid)->first();
            $data->nama_divisi = $request->input('nama_divisi');
            $data->save();
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'failed',
                'errors' => $th->getMessage()
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Berhasil memperbaharui data ',
            'data' => $data
        ]);
    }

    public function deleteData($uuid)
    {
        if (!Uuid::isValid($uuid)) {
            return response()->json([
                'code' => 400,
                'message' => 'UUID tidak ditemukan'
            ]);
        }
        try {
            $data = DivisiModel::where('uuid', $uuid)->first();

            if (!$data) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
            $data->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'failed',
                'errors' => $th->getMessage()
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Berhasil delete data'
        ]);
    }

    public function testTimezone()
    {
        $currentTime = now();
        return "Current time in Jakarta: " . $currentTime->format('Y-m-d H:i:s');
    }
}
