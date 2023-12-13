<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('magento_oauth_keys', function (Blueprint $table): void {
            $table->id();
            $table->string('magento_connection')->index();

            $table->json('keys');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magento_oauth_keys');
    }
};
