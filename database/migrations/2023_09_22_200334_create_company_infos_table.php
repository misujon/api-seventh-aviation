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
        Schema::create('company_infos', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('company_name');
            $table->string('company_email')->nullable();
            $table->string('purpose')->default('STANDARD');
            $table->string('device_type')->default('MOBILE');
            $table->string('country_calling_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_postal')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_country_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_infos');
    }
};
