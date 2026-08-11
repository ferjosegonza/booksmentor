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
        Schema::create('ensenanzas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('libro_id')->constrained('libros')->onDelete('cascade');
            $table->integer('orden');
            $table->text('texto_original');
            $table->string('tema')->nullable();
            $table->timestamps();

            $table->unique(['libro_id', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ensenanzas');
    }
};