<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kota extends Model
{
    use HasFactory;

    protected $table = 'kota';
    protected $fillable = ['nama_kota', 'deskripsi'];

    public function wisatawan()
    {
        return $this->hasMany(Wisatawan::class);
    }

    // Mendapatkan top 5 kota berdasarkan jumlah kunjungan
    public function getTopKota()
    {
        return $this->with('wisatawan')->select('kota.*', DB::raw('SUM(wisatawan.jumlah_kunjungan) as total_kunjungan'))->join('wisatawan', 'kota.id', '=', 'wisatawan.kota_id')->groupBy('kota.id', 'kota.nama_kota')->orderByDesc('total_kunjungan')->limit(5)->get();
    }

    // Mendapatkan total kunjungan di semua kota
    public function getTotalKunjungan()
    {
        return $this->withSum('wisatawan', 'jumlah_kunjungan')->get()->sum('wisatawan_sum_jumlah_kunjungan');
    }

    // Mendapatkan data grafik berdasarkan kebangsaan
    public function getGrafikData()
    {
        return DB::table('wisatawan')
            ->select('negara_id', 'bulan', 'tahun', DB::raw('SUM(jumlah_kunjungan) as jumlah_kunjungan'))
            ->groupBy('negara_id', 'bulan', 'tahun')
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
            ->groupBy('negara_id')
            ->toArray();
    }
}
