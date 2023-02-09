<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('pass')->nullable()->default(null);
            $table->string('first_name')->nullable()->default(null);
            $table->string('last_name')->nullable()->default(null);
            $table->string('image')->nullable()->default(null);
            $table->string('cover')->nullable()->default(null);
            $table->unsignedBigInteger('student_id')->nullable()->default(null);
            $table->unsignedBigInteger('telegram_id')->nullable()->default(null);
            $table->uuid('azure_id')->nullable()->default(null);
            $table->foreignId('group_id')->nullable()->default(null)->constrained()->nullOnDelete();
            $table->enum('role', ['student', 'headman', 'professor', 'admin'])->default('student');
            $table->enum('locale', ['en', 'ru', 'ua'])->default('ua');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
