<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUbigeosTable extends Migration
{
    public function up()
    {
        Schema::create('ubigeos', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_interno');
            $table->string('codigo', 64);
            $table->tinyInteger('estado')->default('0');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ubigeos');
    }
}
