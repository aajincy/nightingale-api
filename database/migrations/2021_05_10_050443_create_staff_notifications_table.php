<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('system_notification_id');
            
            $table->boolean('sms')->default(false);
            $table->boolean('email')->default(false);
            $table->boolean('push_notification')->default(false);
            $table->boolean('in_app_notification')->default(false);

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('system_notification_id')->references('id')->on('system_notifications'); 
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
        Schema::dropIfExists('staff_notifications');
    }
}
