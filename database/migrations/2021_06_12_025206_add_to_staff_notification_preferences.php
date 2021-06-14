<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToStaffNotificationPreferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_notification_preferences', function (Blueprint $table) {
            $table->mediumText('inappnotification');
            $table->mediumText('emailnotification');
            $table->mediumText('pushnotification');
            $table->string('scheduleday',500);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_notification_preferences', function (Blueprint $table) {
            $table->dropIfExists('inappnotification');
            $table->dropIfExists('emailnotification');
            $table->dropIfExists('pushnotification');
            $table->dropIfExists('scheduleday');
        });
    }
}
