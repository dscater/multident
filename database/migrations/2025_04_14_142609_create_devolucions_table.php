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
        Schema::create('devolucions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("sucursal_id");
            $table->unsignedBigInteger("orden_venta_id");
            $table->unsignedBigInteger("producto_id");
            $table->unsignedBigInteger("detalle_orden_id");
            $table->string("razon");
            $table->text("descripcion")->nullable();
            $table->date("fecha_registro");
            $table->timestamps();

            $table->foreign("sucursal_id")->on("sucursals")->references("id");
            $table->foreign("orden_venta_id")->on("orden_ventas")->references("id");
            $table->foreign("producto_id")->on("productos")->references("id");
            $table->foreign("detalle_orden_id")->on("detalle_ordens")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devolucions');
    }
};
