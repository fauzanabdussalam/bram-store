<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {      
            $table->id();
            $table->string('phone', 15);
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->unsignedBigInteger('city')->nullable();
            $table->foreign('city')->references('id')->on('kota')->onDelete('NO ACTION');
            $table->text('address')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender', 15)->nullable();
            $table->text('picture')->nullable();
            $table->text('password');
            $table->rememberToken();
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
        Schema::dropIfExists('customer');
    }
}
