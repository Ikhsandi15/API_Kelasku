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
        Schema::create('friendships', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('request_friendship');
            $table->uuid('accept_friendship');
            $table->enum('status', ['pending', 'accept', 'reject'])->default('pending');
            $table->timestamps();

            $table->foreign('request_friendship')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('accept_friendship')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
