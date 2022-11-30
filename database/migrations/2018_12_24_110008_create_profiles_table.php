<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->references('id')->on('users');
            $table->integer('region_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->char('tel', 25)->nullable();
            $table->date('birthday')->nullable();
            $table->enum('gender', ['man', 'woman']);
            $table->text('about')->nullable();
            $table->boolean('is_debtor')->nullable();
            $table->integer('debt_sum')->default(0);
            $table->json('debt_orders')->nullable();
            $table->integer('discount')->default(0);
            $table->integer('bonus')->default(0);
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
        Schema::dropIfExists('profiles');
    }
}
