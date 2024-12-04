<?php

namespace App\Http\Controllers;

use App\Models\Kota;
use App\Models\Negara;
use App\Models\Wisatawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WisatawanController extends Controller
{
    public function dashBoard()
    {
        $kota = Kota::with(['wisatawan.negara'])->get();
        $topKota = (new Kota)->getTopKota();
        $totalKunjungan = (new Kota)->getTotalKunjungan();
        $negara = Negara::all();
        $grafikData = (new Kota)->getGrafikData();
        $topCountries = (new Negara)->getTopCountries();
        $country = DB::table('negara')->pluck('nama_negara', 'id');
        // Menyiapkan data JSON untuk Chart.js dengan pluck nama negara dan jumlah kunjungan
        $countries = $topCountries->pluck('negara.nama_negara')->toArray();
        $visits = $topCountries->pluck('total_kunjungan')->toArray();

        return view('dashboard', compact('kota', 'grafikData', 'negara', 'totalKunjungan', 'topCountries', 'topKota', 'countries', 'visits', 'country'));
    }

    public function getCountry($countryId)
    {
        $data = (new Wisatawan)->getCountryData($countryId);
        return response()->json($data); // Mengembalikan data lengkap untuk Chart.js
    }

    public function showUploadForm()
    {
        return view('upload');
    }

    public function importCsv(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt']);
        $wisatawan = new Wisatawan();
        $wisatawan->importCsv($request->file('file'), ['Malaysia', 'Filipina', 'Singapura', 'Thailand', 'Vietnam', 'Tiongkok', 'India', 'Australia']);
        
        return back()->with('success', 'CSV Data Imported Successfully!');
    }

    public function report()
    {
        $kotas = Kota::all();
        $negaras = Negara::all();
        $wisatawans = Wisatawan::all();
        return view('report.report', compact('kotas', 'negaras', 'wisatawans'));
    }

    public function search(Request $request)
    {
        $query = Wisatawan::query();

        if ($request->kota_id) $query->where('kota_id', $request->kota_id);
        if ($request->negara_id) $query->where('negara_id', $request->negara_id);
        if ($request->month) $query->where('bulan', $request->month);
        if ($request->year) $query->where('tahun', $request->year);

        $results = $query->with(['kota', 'negara'])->paginate(20);
        return response()->json($results); // JSON response lengkap untuk data pencarian
    }

    public function downloadPDF(Request $request)
    {
        $query = DB::table('wisatawan')
            ->join('kota', 'wisatawan.kota_id', '=', 'kota.id')
            ->join('negara', 'wisatawan.negara_id', '=', 'negara.id')
            ->select('kota.nama_kota', 'wisatawan.bulan', 'wisatawan.tahun', 'negara.nama_negara', 'wisatawan.jumlah_kunjungan');

        if ($request->filled('kota_id')) $query->where('kota.id', $request->kota_id);
        if ($request->filled('month')) $query->where('wisatawan.bulan', $request->month);
        if ($request->filled('year')) $query->where('wisatawan.tahun', $request->year);
        if ($request->filled('negara_id')) $query->where('negara.id', $request->negara_id);

        $data = $query->get();
        $pdf = Pdf::loadView('report.pdf', compact('data'));
        return $pdf->download('laporan_kunjungan.pdf');
    }
    
}
