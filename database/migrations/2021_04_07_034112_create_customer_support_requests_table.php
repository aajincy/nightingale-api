<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerSupportRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_support_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('hospital_id');
            $table->string('reason_for_contact');
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->longText('message'); 
            $table->string('status')->default('Inprogress');
            $table->foreign('hospital_id')->references('id')->on('hospitals'); 
            $table->foreign('staff_id')->references('id')->on('staff'); 
            $table->foreign('resolved_by')->references('id')->on('users');
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
        Schema::dropIfExists('customer_support_requests');
    }
}
