<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbDisposisiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_disposisi', function (Blueprint $table) {
            $table->id('id_disposisi');
            $table->unsignedBigInteger('id_dokumen');
            $table->unsignedBigInteger('id_user'); // Merujuk ke users (keuangan, manajer, atau atasan)
            $table->enum('status', ['diteruskan', 'ditolak', 'disetujui']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        
            $table->foreign('id_dokumen')->references('id_dokumen')->on('tb_dokumen')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_disposisi');
    }
}
