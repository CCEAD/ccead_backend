<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 64);
            $table->string('apellidos', 64);
            $table->string('name');
            $table->string('email')->unique();
            $table->string('telefono', 16);
            $table->tinyInteger('estado')->default('1');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('temp_password')->nullable();
            $table->rememberToken();
            $table->unsignedBigInteger('agencia_id')->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('agencia_id')->references('id')->on('agencias')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
