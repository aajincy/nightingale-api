<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientTransferLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_transfer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id');
            $table->string('from_room_number',10);
            $table->string('from_to_number',10);
            $table->dateTime('transfer_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_transfer_log');
    }
}
