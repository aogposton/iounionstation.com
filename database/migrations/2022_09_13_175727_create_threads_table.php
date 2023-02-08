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
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->string('query_string');
            $table->unsignedBigInteger('content_id');
            $table->unsignedBigInteger('source_id');
            $table->unsignedBigInteger('status_id');
            $table->json('content_array');
            $table->timestamps();
        });
    }

    public function down()
    {
      Schema::dropIfExists('threads');
    }
};
