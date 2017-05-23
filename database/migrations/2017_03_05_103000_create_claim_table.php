<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClaimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::enableForeignKeyConstraints();
        Schema::create('claims', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('claim_type');
            $table->string('claim_data_id');
            $table->integer('claimer_id')->unsigned();
            $table->foreign('claimer_id')->references('id')
              ->on('users')
              ->onUpdate('cascade')
              ->onDelete('cascade');
            $table->integer('approver_id')->unsigned();
            $table->foreign('approver_id')->references('id')
              ->on('users')
              ->onUpdate('cascade')
              ->onDelete('cascade');
            $table->integer('finance_id')->unsigned();
            $table->foreign('finance_id')->references('id')
              ->on('users')
              ->onUpdate('cascade')
              ->onDelete('cascade');
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
        Schema::dropIfExists('claims');
        Schema::disableForeignKeyConstraints();
    }
}
