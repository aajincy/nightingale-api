<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_id');
            $table->string('name');
            $table->bigInteger('rooms')->default(1);
            $table->bigInteger('nurse_ratio_patient_day')->nullable();
            $table->bigInteger('nurse_ratio_nurse_day')->nullable();
            $table->bigInteger('aides_ratio_patient_day')->nullable();
            $table->bigInteger('aides_ratio_aide_day')->nullable();
            $table->bigInteger('nurse_ratio_patient_night')->nullable();
            $table->bigInteger('nurse_ratio_nurse_night')->nullable();
            $table->bigInteger('aides_ratio_patient_night')->nullable();
            $table->bigInteger('aides_ratio_aide_night')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
    }
}
