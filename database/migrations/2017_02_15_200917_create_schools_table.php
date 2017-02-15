<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary()->unique();
            $table->string('title');
            $table->string('address1');
            $table->string('address2')->nullable();;
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->string('country');
            $table->string('website')->nullable();;
            $table->string('phone')->nullable();;
            $table->string('api_key')->nullable();;
            $table->string('api_secret')->nullable();;
            $table->unsignedInteger('user_quota');
            $table->timestamp('valid_until');
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
        Schema::dropIfExists('schools');
    }
}
