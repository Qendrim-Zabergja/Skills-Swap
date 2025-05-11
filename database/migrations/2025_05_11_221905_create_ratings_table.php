<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('skill_requests')->onDelete('cascade');
            $table->foreignId('rater_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rated_user_id')->constrained('users')->onDelete('cascade');
            $table->integer('score')->between(1, 5);
            $table->text('comment')->nullable();
            $table->timestamps();
            
            // Ensure one rating per user per request
            $table->unique(['request_id', 'rater_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
};
