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
        Schema::create('promocions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("producto_id");
            $table->double("porcentaje", 8, 2);
            $table->date("fecha_ini");
            $table->date("fecha_fin");
            $table->text("descripcion")->nullable();
            $table->date("fecha_registro")->nullable();
            $table->timestamps();

            $table->foreign("producto_id")->on("productos")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promocions');
    }
};
