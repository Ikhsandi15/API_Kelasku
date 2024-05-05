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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            // $table->morphs('tokenable');
            $table->uuid('tokenable_id'); // Menggunakan foreign key yang sesuai dengan ID pengguna 
            $table->string('token', 64)->unique();
            $table->string('tokenable_type'); // Menyimpan tipe model yang memiliki token (biasanya 'App\Models\User')
            $table->string('name');
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('tokenable_id')->references('id')->on('users')->onDelete('cascade'); // Menggunakan foreign key yang sesuai dengan ID pengguna 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
