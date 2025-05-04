<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_unit', function (Blueprint $table) {
            $table->id('id_unit');
            $table->string('nama_unit');
            $table->string('direktur');
            $table->unsignedBigInteger('id_user')->nullable(); // Foreign key to unit
            $table->string('telepon');
            $table->string('logo')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('tb_unit');
    }
}
