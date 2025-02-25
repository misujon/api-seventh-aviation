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
        Schema::table('flight_bookings', function (Blueprint $table) {
            $table->json('flight_offers')->nullable()->after('source');
            $table->json('passengers')->nullable()->after('total_response');
            $table->json('booking_response')->nullable()->after('passengers');
            $table->string('booking_id')->nullable()->after('pnr');
            $table->string('booking_office_id')->nullable()->after('booking_id');
            $table->json('associated_records')->nullable()->after('booking_office_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flight_bookings', function (Blueprint $table) {
            //
        });
    }
};
