<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demo_users', function (Blueprint $table) {
            //uuid for id as primary
            $table->uuid('id')->primary();
            //string for email as unique
            $table->string('email')->unique();
            // string for mobile number unique and nullable
            $table->string('mobile')->unique()->nullable();
            // string for name nullable
            $table->string('name')->nullable();
            // string for focal point name nullable
            $table->string('focal_point_name')->nullable();
            // string for country nullable
            $table->string('country')->nullable();
            ///
            //soft delete
            $table->softDeletes();
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
        Schema::dropIfExists('demo_users');
    }
};
