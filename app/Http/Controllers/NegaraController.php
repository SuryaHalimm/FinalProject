<?php

namespace App\Http\Controllers;

use App\Models\Kota;
use App\Models\Negara;
use Illuminate\Http\Request;

class NegaraController extends Controller
{
    public function getDataNegara(Request $request, $id)
    {
        // Ambil data pengunjung berdasarkan negara yang dipilih
        $negaraId = $request->input('negara_id');

        // Logika untuk mengambil data yang sesuai
        $data = Negara::findOrFail($id)
            ->wisatawan()
            ->where('negara_id', $negaraId)
            ->get();

        return response()->json($data);
    }
}
