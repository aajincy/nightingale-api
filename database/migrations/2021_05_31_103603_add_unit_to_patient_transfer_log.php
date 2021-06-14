<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitToPatientTransferLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_transfer_logs', function (Blueprint $table) {
            $table->dropColumn('from_to_number');
            $table->string('to_room_number',10)->after('from_room_number');
            $table->foreignId('unit_id')->after('patient_id');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_transfer_logs', function (Blueprint $table) {
            $table->dropColumn('to_room_number');
            $table->dropColumn('unit_id');
        });
    }
}
