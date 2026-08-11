<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suscripcion_idiomas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suscripcion_id')->constrained('suscripciones')->onDelete('cascade');
            $table->foreignId('idioma_id')->constrained('cat_idiomas')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['suscripcion_id', 'idioma_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suscripcion_idiomas');
    }
};