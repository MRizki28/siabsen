<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AbsenModel;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function countData()
    {
        $user = User::count();
        $absen = AbsenModel::count();

        return response()->json([
            'code' => 200,
            'message' => 'success count',
            'data' => [
                'user' => $user,
                'absen' => $absen
            ]
        ]);
    }
}
