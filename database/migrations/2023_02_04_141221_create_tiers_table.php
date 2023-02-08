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
    Schema::create('tiers', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->integer('monthly_email_limit');
      $table->integer('thread_limit');
      $table->double('price', 4, 2);
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
    Schema::dropIfExists('tiers');
  }
};
