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
        Schema::create('sucursals', function (Blueprint $table) {
            $table->id();
            $table->string("codigo")->unique();
            $table->string("nombre", 500);
            $table->string("direccion", 600)->nullable();
            $table->string("fonos", 600)->nullable();
            $table->unsignedBigInteger("user_id");
            $table->date("fecha_registro");
            $table->integer("status")->default(1);
            $table->timestamps();

            $table->foreign("user_id")->on("users")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursals');
    }
};
