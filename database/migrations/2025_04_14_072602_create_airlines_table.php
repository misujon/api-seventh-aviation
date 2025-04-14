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
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('iata_code')->unique();
            $table->string('lcc')->nullable();
            $table->string('name');
            $table->text('logo')->nullable();
            $table->text('large_logo')->nullable();
            $table->enum('is_featured', ['TRUE', 'FALSE'])->default('FALSE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airlines');
    }
};
