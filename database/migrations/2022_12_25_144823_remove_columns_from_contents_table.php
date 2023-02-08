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
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('link');
            $table->dropColumn('published_at');
            $table->dropColumn('author');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->text('title')->nullable();
            $table->text('link')->nullable();
            $table->string('published_at')->nullable();
            $table->text('author')->nullable();
        });
    }
};
