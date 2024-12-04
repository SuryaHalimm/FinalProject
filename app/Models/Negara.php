<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Negara extends Model
{
    use HasFactory;

    protected $table = 'negara';
    protected $fillable = ['nama_negara'];

    public function wisatawan()
    {
        return $this->hasMany(Wisatawan::class);
    }

    // Mendapatkan top negara pengunjung
    public function getTopCountries()
    {
        return Wisatawan::select('negara_id', DB::raw('SUM(jumlah_kunjungan) as total_kunjungan'))
            ->with('negara')
            ->groupBy('negara_id')
            ->orderByDesc('total_kunjungan')
            ->get();
    }
}
