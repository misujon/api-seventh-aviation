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
        Schema::table('users', function (Blueprint $table) {
            $table->string('title')->nullable()->after('token');
            $table->string('last_name')->nullable()->after('title');
            $table->date('dob')->nullable()->after('last_name');
            $table->string('phone')->nullable()->after('dob');
            $table->string('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->nullable()->after('city');
            $table->string('country_code')->nullable()->after('country');
            $table->string('passport_no')->nullable()->after('country_code');
            $table->date('expiery_date')->nullable()->after('passport_no');
            $table->date('issue_date')->nullable()->after('expiery_date');
            $table->string('issue_place')->nullable()->after('issue_date');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('INACTIVE')->after('issue_place');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
