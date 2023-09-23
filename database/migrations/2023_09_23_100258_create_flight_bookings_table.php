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
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('search_id');
            $table->string('flight_id');
            $table->string('flight_id_string');
            $table->string('customer_id')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('pnr')->nullable();
            $table->unsignedBigInteger('price_currency');
            $table->unsignedBigInteger('base_price');
            $table->unsignedBigInteger('total_price');
            $table->unsignedBigInteger('grand_total_price');
            $table->unsignedBigInteger('billing_currency');
            $table->dateTime('last_ticketing_date');
            $table->enum('instant_ticketing', ['TRUE', 'FALSE']);
            $table->string('source', 25);
            $table->json('itineraries');
            $table->json('pricing');
            $table->json('traveler_pricing');
            $table->json('booking_requirements')->nullable();
            $table->json('dictionaries')->nullable();
            $table->enum('status', ['PENDING', 'BOOKED', 'TICKETED', 'CANCELLED'])->default('PENDING');
            $table->string('cancellation_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_bookings');
    }
};
