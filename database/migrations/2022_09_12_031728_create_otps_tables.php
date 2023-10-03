<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTables extends Migration
{
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('otp');
            $table->bigInteger('usuario_id');
            $table->timestamp('expire_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('otps');
    }
}
