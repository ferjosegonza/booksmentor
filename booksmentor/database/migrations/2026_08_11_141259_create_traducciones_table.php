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
        Schema::create('traducciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ensenanza_id')->constrained('ensenanzas')->onDelete('cascade');
            $table->foreignId('idioma_id')->constrained('cat_idiomas')->onDelete('cascade');
            $table->text('texto_traducido');
            $table->date('fecha_traduccion')->useCurrent();
            $table->integer('veces_usado')->default(0);
            $table->date('ultimo_uso')->nullable();
            $table->timestamps();

            $table->unique(['ensenanza_id', 'idioma_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traducciones');
    }
};