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
        Schema::create('producto_sucursals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("sucursal_id");
            $table->unsignedBigInteger("producto_id");
            $table->double("stock_actual");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_sucursals');
    }
};
