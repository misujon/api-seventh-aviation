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
            $table->enum('trip_type', ['ONE_WAY', 'ROUND_TRIP', 'MULTI_STOP'])->default('ONE_WAY')->after('flight_id_string');
            $table->unsignedInteger('pax_adults')->default(1)->after('trip_type');
            $table->unsignedInteger('pax_childs')->nullable()->after('pax_adults');
            $table->unsignedInteger('pax_kids')->nullable()->after('pax_childs');
            $table->unsignedInteger('pax_infants')->nullable()->after('pax_kids');
            $table->enum('payment_status', ['PENDING', 'PROCESSING', 'SUCCESS', 'FAILED'])->default('PENDING')->after('status');
            $table->text('payment_id')->nullable()->unique()->after('payment_status');
            $table->text('payment_url')->nullable()->after('payment_id');
            $table->longText('payment_response')->nullable()->after('payment_url');
            $table->text('payment_failed_reason')->nullable()->after('payment_response');
            $table->string('payment_session')->nullable()->after('payment_failed_reason');
            $table->text('payment_full_response')->nullable()->after('payment_session');
            $table->string('customer_name')->nullable()->after('customer_email');
            $table->string('customer_address')->nullable()->after('customer_name');
            $table->string('customer_city')->nullable()->after('customer_address');
            $table->string('customer_state')->nullable()->after('customer_city');
            $table->string('customer_postcode')->nullable()->after('customer_state');
            $table->string('customer_country')->nullable()->after('customer_postcode');
            $table->string('customer_phone')->nullable()->after('customer_country');
            $table->string('customer_product_name')->nullable()->after('customer_phone');
            $table->string('customer_product_category')->nullable()->after('customer_product_name');
            $table->string('customer_product_profile')->nullable()->after('customer_product_category');
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
