<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingreso_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("ingreso_producto_id");
            $table->unsignedBigInteger("producto_id");
            $table->double("cantidad");
            $table->unsignedBigInteger("ubicacion_producto_id");
            $table->date("fecha_vencimiento")->nullable();
            $table->text("descripcion")->nullable();
            $table->date("fecha_registro")->nullable();
            $table->timestamps();

            $table->foreign("ingreso_producto_id")->on("ingreso_productos")->references("id");
            $table->foreign("producto_id")->on("productos")->references("id");
            $table->foreign("ubicacion_producto_id")->on("ubicacion_productos")->references("id");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingreso_detalles');
    }
};
