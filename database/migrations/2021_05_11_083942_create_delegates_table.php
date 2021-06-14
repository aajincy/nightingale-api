<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelegatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delegates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->foreign('room_id')->references('id')->on('rooms'); 
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units'); 
            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients'); 
            $table->unsignedBigInteger('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff'); 
            $table->string('shift');
            $table->date('assigned_date');
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
        Schema::dropIfExists('delegates');
    }
}
