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
        Schema::create('subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('title');
            $table->string('notes')->nullable()->default(null);
            $table->foreignId('professor_id')->nullable()->default(null)->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->default(null)->constrained()->cascadeOnDelete();
            $table->string('professor_name')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects');
    }
};
