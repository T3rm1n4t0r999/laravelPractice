<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_upload_success()
    {
        // Создаем временный файл для загрузки
        Storage::fake('public');

        $response = $this->post(route('upload.perform'), [
            'file' => UploadedFile::fake()->image('test.jpg'), // Используем фейковый файл
        ]);

        // Проверяем статус ответа
        $response->assertStatus(200);

        // Проверяем, что файл был загружен
        Storage::disk('public')->assertExists('userFiles/test.jpg');

        // Проверяем, что запись добавлена в базу данных
        $this->assertDatabaseHas('user_files', [
            'filename' => 'test.jpg',
            'user_id' => 1, // Замените на актуальный ID пользователя
        ]);
    }

    public function test_file_upload_validation_failure()
    {
        $response = $this->post(route('upload.perform'), [
            'file' => '', // Отправляем пустое значение
        ]);

        // Проверяем статус ответа и сообщение об ошибке
        $response->assertJsonValidationErrors('file');
    }
}
