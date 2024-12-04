<?php

namespace App\Http\Controllers;

use App\Models\Kota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KotaController extends Controller
{
    //
    public function view()
    {
        $kota = Kota::all();
        return view('Kota.index', compact('kota'));
    }

    public function viewLandingPage()
    {
        $kota = Kota::all()->take(3);
        return view('welcome', compact('kota'));
    }

    public function kotaInfo(string $id)
    {
        $id = decrypt($id);
        $kota = Kota::find($id);
        $negaraPengunjung = DB::table('wisatawan')
            ->leftJoin('negara', 'wisatawan.negara_id', '=', 'negara.id')
            ->where('wisatawan.kota_id', $kota->id)
            ->select('negara.nama_negara as nama_negara', DB::raw('SUM(wisatawan.jumlah_kunjungan) as total_kunjungan'))
            ->groupBy('wisatawan.negara_id', 'negara.nama_negara')
            ->orderBy('total_kunjungan', 'desc')
            ->take(5)
            ->get();
        $data = [];
        try {
            // Panggil API Flask untuk mendapatkan prediksi
            $response = Http::get('http://127.0.0.1:5000/api/predictions', [
                'city' => $kota->nama_kota,
            ]);

            // Jika berhasil, ambil data dari response API
            if ($response->successful() && !empty($response->json())) {
                $data = $response->json();
            }
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan pada pemanggilan API
            Log::error('Failed to fetch data from Flask API: ' . $e->getMessage());
        }
        return view('Kota.info-kota.index', compact('kota', 'negaraPengunjung', 'data'));
    }

    // Menyimpan data kota baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kota' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $data = $request->only(['nama_kota', 'deskripsi']);
        if ($request->hasFile('gambar')) {
    
            // Simpan gambar baru
            $fileName = $request->nama_kota . '.' . $request->file('gambar')->getClientOriginalExtension();
            $filePath = $request->file('gambar')->storeAs('image', $fileName, 'public'); // Simpan di storage/app/public/image
    
            // Simpan nama file ke data untuk diupdate di database
            $data['gambar'] = $fileName;
        }
    
        Kota::create($data); // Simpan data kota tanpa kolom gambar

        return redirect()->route('kota.view')->with('success', 'Kota berhasil ditambahkan.');
    }

    // Mengambil data kota untuk diedit (opsional untuk penggunaan di modal)
    public function edit($id)
    {
        $kota = Kota::findOrFail($id);
        return view('kota.edit', compact('kota')); // Hanya perlu jika Anda ingin menggunakan halaman terpisah untuk edit
    }

    // Memperbarui data kota yang sudah ada
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kota' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $id = decrypt($id);
        $kota = Kota::findOrFail($id);
        $data = $request->only(['nama_kota', 'deskripsi']);
    
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            $oldFilePath = 'public/image/' . $kota->gambar; // Gunakan properti gambar dari model Kota
            if (Storage::exists($oldFilePath)) {
                Storage::delete($oldFilePath);
            }
    
            // Simpan gambar baru
            $fileName = $request->nama_kota . '.' . $request->file('gambar')->getClientOriginalExtension();
            $filePath = $request->file('gambar')->storeAs('image', $fileName, 'public'); // Simpan di storage/app/public/image
    
            // Simpan nama file ke data untuk diupdate di database
            $data['gambar'] = $fileName;
        }
    
        $kota->update($data);

        return redirect()->route('kota.view')->with('success', 'Kota berhasil diperbarui.');
    }

    // Menghapus data kota
    public function destroy($id)
    {
        $id = decrypt($id);
        $kota = Kota::findOrFail($id);
        $kota->delete();

        return redirect()->route('kota.view')->with('success', 'Kota berhasil dihapus.');
    }
}
