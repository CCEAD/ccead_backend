<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajasTable extends Migration
{
    public function up()
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->integer('gestion');
            $table->string('cod_interno', 64);
            $table->char('cod_almacen', 36);
            $table->unsignedBigInteger('agencia_id')->unsigned();
            $table->unsignedBigInteger('ubigeo_id')->unsigned()->nullable();
            $table->tinyInteger('estado')->default('0');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('agencia_id')->references('id')->on('agencias')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('ubigeo_id')->references('id')->on('ubigeos')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cajas');
    }
}
