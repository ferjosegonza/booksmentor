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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('nombre')->nullable();
            $table->foreignId('frecuencia_id')->nullable()->constrained('cat_frecuencias')->nullOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('cat_planes')->nullOnDelete();
            $table->time('hora_envio')->nullable();
            $table->string('zona_horaria')->nullable();
            $table->date('fecha_registro')->useCurrent();
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
        Schema::dropIfExists('usuarios');
    }
};