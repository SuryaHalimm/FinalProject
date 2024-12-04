<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wisatawan extends Model
{
    use HasFactory;

    protected $table = 'wisatawan';
    protected $fillable = ['kota_id', 'negara_id', 'bulan', 'tahun', 'jumlah_kunjungan'];

    public function kota()
    {
        return $this->belongsTo(Kota::class);
    }

    public function negara()
    {
        return $this->belongsTo(Negara::class);
    }

    // Import data dari file CSV
    public function importCsv($file, $countries)
    {
        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $bulanTahun = $data[0];
                $namaKota = $data[1];
                $bulan = substr($bulanTahun, 0, 3);
                $tahun = 2000 + (int) substr($bulanTahun, -2);
                $kota = Kota::firstOrCreate(['nama_kota' => $namaKota]);

                for ($i = 0; $i < count($countries); $i++) {
                    $jumlah_kunjungan = $data[$i + 2];

                    if ($jumlah_kunjungan != '0' && $jumlah_kunjungan != '') {
                        $negara = Negara::firstOrCreate(['nama_negara' => $countries[$i]]);

                        $existingData = $this->where('kota_id', $kota->id)
                            ->where('negara_id', $negara->id)
                            ->where('bulan', $bulan)
                            ->where('tahun', $tahun)
                            ->first();

                        if ($existingData) {
                            $existingData->update([
                                'jumlah_kunjungan' => (int) str_replace(',', '', $jumlah_kunjungan),
                            ]);
                        } else {
                            $this->create([
                                'bulan' => $bulan,
                                'tahun' => $tahun,
                                'kota_id' => $kota->id,
                                'negara_id' => $negara->id,
                                'jumlah_kunjungan' => (int) str_replace(',', '', $jumlah_kunjungan),
                            ]);
                        }
                    }
                }
            }
            fclose($handle);
        }
    }

    // Mendapatkan data pengunjung berdasarkan negara untuk chart.js
    public function getCountryData($countryId)
    {
        return $this->where('negara_id', $countryId)
            ->select('bulan', 'tahun', DB::raw('SUM(jumlah_kunjungan) as jumlah_kunjungan'))
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun')
            ->orderByRaw(
                "
                CASE bulan
                    WHEN 'Jan' THEN 1
                    WHEN 'Feb' THEN 2
                    WHEN 'Mar' THEN 3
                    WHEN 'Apr' THEN 4
                    WHEN 'May' THEN 5
                    WHEN 'Jun' THEN 6
                    WHEN 'Jul' THEN 7
                    WHEN 'Aug' THEN 8
                    WHEN 'Sep' THEN 9
                    WHEN 'Oct' THEN 10
                    WHEN 'Nov' THEN 11
                    WHEN 'Dec' THEN 12
                END
            ",
            )
            ->get()
            ->toArray();
    }
}
