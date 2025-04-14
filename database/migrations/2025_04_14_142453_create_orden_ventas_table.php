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
        Schema::create('orden_ventas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("nro");
            $table->unsignedBigInteger("sucursal_id");
            $table->unsignedBigInteger("cliente_id");
            $table->string("factura");
            $table->string("tipo_pago");
            $table->date("fecha_registro")->nullable();
            $table->integer("status")->default(1);
            $table->timestamps();

            $table->foreign("sucursal_id")->on("sucursals")->references("id");
            $table->foreign("cliente_id")->on("clientes")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_ventas');
    }
};
