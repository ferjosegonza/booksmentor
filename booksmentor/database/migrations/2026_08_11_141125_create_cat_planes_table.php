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
        Schema::create('cat_planes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('max_libros');
            $table->integer('max_idiomas');
            $table->boolean('permite_audio');
            $table->decimal('precio_mensual', 8, 2);
            $table->integer('orden');
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
        Schema::dropIfExists('cat_planes');
    }
};