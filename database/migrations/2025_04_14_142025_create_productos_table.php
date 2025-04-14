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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string("nombre", 500);
            $table->string("descripcion", 900);
            $table->decimal("precio_pred", 24, 2);
            $table->decimal("precio_min", 24, 2);
            $table->decimal("precio_fac", 24, 2);
            $table->decimal("precio_sf", 24, 2);
            $table->double("stock_maximo");
            $table->string("foto", 255)->nullable();
            $table->integer("status")->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
