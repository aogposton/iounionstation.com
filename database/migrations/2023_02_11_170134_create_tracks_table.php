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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cargo_id')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('track_type_id')->nullable();
            $table->unsignedBigInteger('destination_id');
            $table->boolean('accumulate')->default(0);
            $table->json('content_array');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('frequency_id')->nullable();
            $table->timestamp('last_processed_at')->nullable();
            $table->timestamp('process_at')->nullable();
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
        Schema::dropIfExists('tracks');
    }
};
