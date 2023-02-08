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
        Schema::table('function_time_trackers', function (Blueprint $table) {
            //
            $table->string('notes')->nullable();
            $table->Integer('status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('function_time_trackers', function (Blueprint $table) {
            $table->dropColumn('notes');
            $table->dropColumn('status_id');
        });
    }
};
