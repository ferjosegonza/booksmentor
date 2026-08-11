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
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('libro_id')->constrained('libros')->onDelete('cascade');
            $table->foreignId('estado_id')->default(1)->constrained('cat_estados_suscripcion');
            $table->integer('ultima_ensenanza_enviada')->default(0);
            $table->dateTime('fecha_proximo_envio')->nullable();
            $table->timestamps();

            $table->unique(['usuario_id', 'libro_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suscripciones');
    }
};