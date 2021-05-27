<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Createalltables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('cpf')->unique();
            $table->string('password');
        });

        Schema::create('MURALAVISOS', function(Blueprint $table) {
            $table->id();
            $table->integer('ID_USUARIO');
            $table->string('TITULO');
            $table->string('BODY');
            $table->datetime('DATACRIACAO');
        });

        Schema::create('MEDICOS', function(Blueprint $table) {
            $table->id();
            $table->string('NOME');
            $table->string('ENDERECO');
            $table->string('TELEFONE');
            $table->string('CRM');
            $table->string('DIA');
            $table->time('HORA');
            $table->integer('ID_PERIODO');
        });

        Schema::create('PERIODO', function(Blueprint $table) {
            $table->id();
            $table->string('NOME');
        });

        Schema::create('MEDICOSCOMENTARIOS', function(Blueprint $table) {
            $table->id();
            $table->integer('ID_MEDICO');
            $table->datetime('DATACRIACAO');
            $table->string('BODY');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('USERS');
        Schema::dropIfExists('MURALAVISOS');
        Schema::dropIfExists('MEDICOS');
        Schema::dropIfExists('PERIODO');
        Schema::dropIfExists('MEDICOSCOMENTARIOS');
    }
}
