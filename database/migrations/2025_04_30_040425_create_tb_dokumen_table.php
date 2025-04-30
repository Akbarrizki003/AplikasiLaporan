<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbDokumenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('tb_dokumen', function (Blueprint $table) {
    $table->id('id_dokumen');
    $table->unsignedBigInteger('id_unit');
    $table->string('nama_dokumen');
    $table->date('tanggal_upload');
    $table->string('file');
    $table->enum('status', ['dikirim', 'diterima_keuangan', 'diteruskan_ke_manejer', 'disetujui_manejer', 'ditolak_manejer', 'diteruskan_ke_atasan', 'disetujui_atasan', 'ditolak_atasan'])->default('dikirim');
    $table->text('catatan')->nullable();
    $table->timestamps();

    $table->foreign('id_unit')->references('id_unit')->on('tb_unit')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_dokumen');
    }
}
