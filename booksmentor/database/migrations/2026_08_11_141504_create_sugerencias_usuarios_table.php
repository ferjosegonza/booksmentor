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
        Schema::create('sugerencias_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('email')->nullable();
            $table->foreignId('tipo_id')->constrained('cat_tipos_sugerencia');
            $table->string('libro_sugerido')->nullable();
            $table->text('mensaje');
            $table->string('adjunto_url')->nullable();
            $table->boolean('leido')->default(false);
            $table->boolean('atendido')->default(false);
            $table->dateTime('fecha_envio')->useCurrent();
            $table->dateTime('fecha_respuesta')->nullable();
            $table->text('respuesta_admin')->nullable();
            $table->timestamps();

            $table->index(['usuario_id', 'leido', 'atendido']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sugerencias_usuarios');
    }
};