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
        Schema::create('clients', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('companyName');
            $table->index('companyName');
            $table->string('cnpj');
            $table->string('address');
            $table->string('primaryContact');
            $table->string('phone');
            $table->string('email');
            $table->string('avatarUrl')->nullable();
            $table->string('agentUrl')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
};
