<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarpetasTable extends Migration
{
    public function up()
    {
        Schema::create('carpetas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caja_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('caja_id')->references('id')->on('cajas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('carpetas');
    }
}
