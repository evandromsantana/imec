<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->default('default.png');
            $table->string('email')->unique();
            $table->string('password');
        });
        Schema::create('usersfavorites', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_mechanic');
        });
        Schema::create('usersappointments', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_mechanic');
            $table->datetime('ap_datetime');
        });
/**
     * Mecânicos.     
     */
        Schema::create('mechanics', function (Blueprint $table) {
            $table->id();
            $table->integer('name');
            $table->string('avatar')->default('default.png');
            $table->float('stars')->default(0);
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
        });
        Schema::create('mechanicphotos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_mechanic');
            $table->string('url');            
        });
        Schema::create('mechanicreviews', function (Blueprint $table) {
            $table->id();
            $table->integer('id_mechanic');
            $table->float('rote');            
        });
        Schema::create('mechanicservices', function (Blueprint $table) {
            $table->id();
            $table->integer('id_mechanic');
            $table->string('name');
            $table->float('price');            
        });
        Schema::create('mechanictestimonials', function (Blueprint $table) {
            $table->id();
            $table->integer('id_mechanic');
            $table->string('name');
            $table->float('rate');
            $table->string('body');            
        });
        Schema::create('mechanicavailability', function (Blueprint $table) {
            $table->id();
            $table->integer('id_mechanic');
            $table->integer('weekday');
            $table->text('hours');         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('usersfavorites');
        Schema::dropIfExists('usersappointments');
        Schema::dropIfExists('mechanics');
        Schema::dropIfExists('mechanicphotos');
        Schema::dropIfExists('mechanicreviews');
        Schema::dropIfExists('mechanicservices');
        Schema::dropIfExists('mechanictestimonials');
        Schema::dropIfExists('mechanicavailability');
    }
}
