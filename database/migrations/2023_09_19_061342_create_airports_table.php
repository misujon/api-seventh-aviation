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
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50);
            $table->string('name', 200);
            $table->string('cityCode', 50)->nullable();
            $table->string('cityName', 200)->nullable();
            $table->string('countryName', 200)->nullable();
            $table->string('countryCode', 200)->nullable();
            $table->string('timezone', 8)->nullable();
            $table->string('lat', 32)->nullable();
            $table->string('lon', 32)->nullable();
            $table->unsignedInteger('numAirports')->nullable();
            $table->enum('city', ['true', 'false'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airports');
    }
};
