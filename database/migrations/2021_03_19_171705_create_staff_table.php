<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('hospital_id');
            $table->longText('image')->nullable();
            $table->string('type');
            $table->string('roles');
            $table->string('title')->nullable();
            $table->string('certifications')->nullable();
            $table->boolean('sms_notifications')->default(false);
            $table->string('experience')->nullable();
            $table->date('start_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff');
    }
}
