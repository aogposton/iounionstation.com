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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('credential');
            $table->unsignedBigInteger('destination_type_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('deletable')->default(true);
            $table->string('verify_token')->nullable();
            $table->timestamp('verified_at')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('destinations');
    }
};
