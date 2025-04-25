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
        Schema::create('detalle_proformas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("proforma_id");
            $table->unsignedBigInteger("producto_id");
            $table->unsignedBigInteger("promocion_id");
            $table->double("promocion_descuento", 8, 2)->default(0)->nullable();
            $table->double("cantidad");
            $table->decimal("precio_reg", 24, 2);
            $table->decimal("precio", 24, 2);
            $table->decimal("subtotal", 24, 2);
            $table->integer("status")->default(1);
            $table->timestamps();
            $table->foreign("proforma_id")->on("proformas")->references("id");
            $table->foreign("producto_id")->on("productos")->references("id");
            $table->foreign("promocion_id")->on("promocions")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_proformas');
    }
};
