<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class BanHammerCreateTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banhammer_bans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('buid', 24)->unique(); // Unique ban ID

            $table->enum('type', ['username', 'ip']); // The ban type
            $table->string('address')->unique(); // The address being banned (username or IP)

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
        Schema::dropIfExists('banhammer_bans');
    }
}
