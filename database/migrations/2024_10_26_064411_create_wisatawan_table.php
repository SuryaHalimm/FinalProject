<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wisatawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kota_id')->constrained('kota')->onDelete('cascade');
            $table->foreignId('negara_id')->constrained('negara')->onDelete('cascade');
            $table->string('bulan');
            $table->integer('tahun');
            $table->integer(column: 'jumlah_kunjungan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wisatawan', function (Blueprint $table) {
            $table->dropForeign(['kota_id']);
            $table->dropForeign(['negara_id']);
            $table->dropColumn(['kota_id', 'negara_id', 'bulan', 'jumlah_kunjungan']);
        });
    }
};
