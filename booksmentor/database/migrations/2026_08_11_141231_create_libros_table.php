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
        Schema::create('libros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('autor');
            $table->text('descripcion')->nullable();
            $table->string('portada_url')->nullable();
            $table->foreignId('idioma_original_id')->constrained('cat_idiomas');
            $table->foreignId('creado_por_usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->integer('anio_publicacion')->nullable();
            $table->integer('cantidad_ensenanzas')->default(0);
            $table->date('fecha_procesamiento')->nullable();
            $table->boolean('activo')->default(true);
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
        Schema::dropIfExists('libros');
    }
};