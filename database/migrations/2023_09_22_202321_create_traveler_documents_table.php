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
        Schema::create('traveler_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('traveler_id');
            $table->enum('type', ['PASSPORT'])->default('PASSPORT');
            $table->string('birth_place');
            $table->string('issue_location');
            $table->date('issue_date');
            $table->string('number');
            $table->date('expiry_date');
            $table->string('issuance_country');
            $table->string('validity_country');
            $table->string('nationality');
            $table->timestamps();

            $table->foreign('traveler_id')->on('travelers')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traveler_documents');
    }
};
