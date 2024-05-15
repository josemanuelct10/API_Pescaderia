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
        Schema::create('lineas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion')->nullable();
            $table->float('cantidad');
            $table->float('precioLinea');
            $table->float('precioUnitario');
            $table->unsignedBigInteger('factura_id')->nullable();
            $table->unsignedBigInteger('carrito_id')->nullable();
            $table->unsignedBigInteger('pescado_id')->nullable();
            $table->unsignedBigInteger('marisco_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lineas');
    }
};
