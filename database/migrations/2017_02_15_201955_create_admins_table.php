<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary()->unique();
            $table->unsignedInteger('school_id');
            $table->string('name_first');
            $table->string('name_last');
            $table->string('name_display');
            $table->string('primary_email');
            $table->string('picture_url');
            $table->boolean('welcome_email_sent')->default(0);
            $table->timestamps();
            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
