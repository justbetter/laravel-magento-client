<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('magento_oauth', function (Blueprint $table) {
            $table->id();
            $table->string('oauth_consumer_key')->unique();
            $table->string('oauth_consumer_secret')->nullable();
            $table->string('oauth_verifier')->nullable();
            $table->string('access_token')->nullable();
            $table->string('access_token_secret')->nullable();
            $table->json('callback');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_oauth');
    }
};
