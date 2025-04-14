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
            $table->double("cantidad");
            $table->decimal("precio", 24, 2);
            $table->decimal("subtotal", 24, 2);
            $table->timestamps();
            $table->foreign("proforma_id")->on("proformas")->references("id");
            $table->foreign("producto_id")->on("productos")->references("id");
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
