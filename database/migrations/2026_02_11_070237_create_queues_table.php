<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('queues', function (Blueprint $table) {
        $table->id();
        $table->string('ticket_number'); // the ticket number
        $table->unsignedBigInteger('counter_id'); // counter id
        $table->string('status'); // e.g., 'serving', 'pending', 'done'
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
public function down()
{
    Schema::dropIfExists('queues');
}

};
