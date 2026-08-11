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
        Schema::create('historial_envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('ensenanza_id')->constrained('ensenanzas')->onDelete('cascade');
            $table->foreignId('idioma_id')->constrained('cat_idiomas')->onDelete('cascade');
            $table->foreignId('estado_id')->default(1)->constrained('cat_estados_envio');
            $table->dateTime('fecha_envio')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial_envios');
    }
};