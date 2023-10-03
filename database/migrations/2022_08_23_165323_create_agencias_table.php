<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgenciasTable extends Migration
{
    public function up()
    {
        Schema::create('agencias', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 64);
            $table->string('nit', 32);
            $table->string('telefono', 32);
            $table->string('direccion', 128);
            $table->string('ciudad', 64);
            $table->string('poder_representacion', 128);
            $table->string('matricula_comercio', 128);
            $table->string('licencia_funcionamiento', 128);
            $table->unsignedBigInteger('representante_id')->unsigned();
            $table->tinyInteger('estado')->default('0');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('representante_id')->references('id')->on('representantes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agencias');
    }
}
