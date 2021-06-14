<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPatientWorkLoadDimensions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_work_load_dimensions', function (Blueprint $table) {
            $table->dropColumn('acuity');
            $table->dropColumn('time');
            $table->dropColumn('note');
            $table->foreignId('hospital_id')->after('patient_id');
            $table->foreignId('staff_id')->after('hospital_id');
            $table->mediumText('work_load')->after('staff_id');

            $table->foreign('hospital_id')->references('id')->on('hospitals');
            $table->foreign('staff_id')->references('id')->on('staff');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_work_load_dimensions', function (Blueprint $table) {
            $table->dropColumn('staff_id');
            $table->dropColumn('work_load');

        });
    }
}
