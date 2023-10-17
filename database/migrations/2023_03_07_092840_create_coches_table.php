<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCochesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coches', function (Blueprint $table) {
            $table->id();
            $table->string('modelo');
            $table->string('color');
            $table->string('matricula' , 7)->unique();
            $table->string('motor');
            $table->string('ruta_img')->default('imagenes/coche_default.jpg');
            $table->foreignId('marca_id')->onDelete('cascade')->constrained();
            $table->foreignId('usuario_id')->onDelete('cascade')->nullable()->constrained();
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
        Schema::dropIfExists('coches');
    }
}
