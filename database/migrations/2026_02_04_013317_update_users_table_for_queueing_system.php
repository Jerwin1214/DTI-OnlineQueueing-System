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
        // Remove unused columns
        $table->dropColumn(['name', 'email', 'email_verified_at', 'remember_token']);

        // Add columns matching WPF login
        $table->string('user_id')->unique()->after('id');
        $table->string('role')->default('counter')->after('password'); // admin / counter
        $table->unsignedBigInteger('counter_id')->nullable()->after('role'); // assigned counter
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('name')->after('id');
        $table->string('email')->unique()->after('name');
        $table->timestamp('email_verified_at')->nullable()->after('email');
        $table->string('remember_token', 100)->nullable()->after('password');

        $table->dropColumn(['user_id', 'role', 'counter_id']);
    });
}

};
