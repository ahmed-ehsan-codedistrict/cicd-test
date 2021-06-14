<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CustomNotifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CompanyNo')->nullable();
            $table->integer('UserId');
            $table->text('Message')->nullable();
            $table->integer('IsRead')->default(0);
            $table->integer('IsDeleted')->default(0);
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
        Schema::dropIfExists('custom_notifications');
    }
}
