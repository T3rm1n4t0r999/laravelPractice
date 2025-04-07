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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('user_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('filename')->unique();
            $table->string('file_link')->nullable();
        });

        Schema::create('link_files', function (Blueprint $table) {
            $table->id(); // Уникальный идентификатор для каждой ссылки
            $table->unsignedBigInteger('user_file_id'); // Внешний ключ к таблице user_files
            $table->string('filename')->nullable();
            $table->string('token')->nullable(); // Токен для доступа к файлу
            $table->boolean('downloadable')->nullable(); // Статус доступности для скачивания
            $table->timestamp('created_at')->nullable(); // Время создания ссылки
            $table->string('password')->nullable(); // Пароль для доступа к файлу

            $table->foreign('user_file_id')->references('id')->on('user_files')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('user_files');
        Schema::dropIfExists('link_files');
    }
};
