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
        Schema::create('producto_relacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("producto_id");
            $table->unsignedBigInteger("producto_relacion");
            $table->timestamps();

            $table->foreign("producto_id")->on("productos")->references("id");
            $table->foreign("producto_relacion")->on("productos")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_relacions');
    }
};
