<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepresentantesTable extends Migration
{
    public function up()
    {
        Schema::create('representantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 64);
            $table->string('apellidos', 64);
            $table->string('telefono', 32);
            $table->string('correo', 128);
            $table->tinyInteger('estado')->default('0');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('representantes');
    }
}
