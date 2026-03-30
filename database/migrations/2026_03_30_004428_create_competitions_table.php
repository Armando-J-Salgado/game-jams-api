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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description', 500);
            $table->string('prize_information', 500);
            $table->string('tools_information', 500);
            $table->integer('max_teams')->default(20);
            $table->integer('total_teams')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_finished')->default(false);
            $table->foreignId('category_id')->constrained();
            $table->unsignedBigInteger('admin_id');
            $table->foreign('admin_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
