<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sort_id');
            $table->integer('region_id');
            $table->integer('currency_id')->nullable();
            $table->char('title');
            $table->char('slug');
            $table->integer('bin')->nullable();
            $table->char('image')->nullable();
            $table->text('about')->nullable();
            $table->string('phones')->nullable();
            $table->string('links')->nullable();
            $table->string('emails')->nullable();
            $table->char('legal_address')->nullable();
            $table->char('actual_address')->nullable();
            $table->boolean('is_supplier')->default(0);
            $table->boolean('is_customer')->default(0);
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('companies');
    }
}
