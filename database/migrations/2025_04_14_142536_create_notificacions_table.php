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
        Schema::create('notificacions', function (Blueprint $table) {
            $table->id();
            $table->text("descripcion");
            $table->date("fecha");
            $table->time("hora");
            $table->string("tipo");
            $table->unsignedBigInteger("sucursal_id");
            $table->unsignedBigInteger("registro_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacions');
    }
};
