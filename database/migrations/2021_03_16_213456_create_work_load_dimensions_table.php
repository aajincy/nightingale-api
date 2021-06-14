<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkLoadDimensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_load_dimensions', function (Blueprint $table) {
            $table->id();
            $table->string('unit_id');
            $table->string('icon')->nullable();
            $table->string('dimension');
            $table->bigInteger('min');
            $table->bigInteger('max');
            $table->boolean('shown')->default(false);
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
        Schema::dropIfExists('work_load_dimensions');
    }
}
