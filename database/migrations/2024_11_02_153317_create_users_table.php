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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("usuario", 255)->unique();
            $table->string("nombres", 255);
            $table->string("apellidos", 255);
            $table->string("ci")->nullable();
            $table->string("ci_exp", 155)->nullable();
            $table->string("correo", 255)->unique();
            $table->string('password');
            $table->unsignedBigInteger("role_id")->nullable();
            $table->integer("sucurals_todo")->default(0);
            $table->unsignedInteger("sucursal_id")->nullable();
            $table->string("foto", 255)->nullable();
            $table->date("fecha_registro");
            $table->integer("acceso");
            $table->integer("status")->default(1);
            $table->timestamps();

            $table->foreign("role_id")->on("roles")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
