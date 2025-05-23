<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function Laravel\Prompts\password;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function getUserFiles(Request $request)
    {
        // Получаем файлы пользователя и объединяем с токенами
        $files = DB::table('files as uf')
            ->leftJoin('links as lf', 'uf.id', '=', 'lf.file_id')
            ->where('uf.user_id', Auth::id())
            ->select(
                'uf.id as file_id',
                'uf.filename',
                'lf.token',
                'lf.downloadable',
                'lf.password'
            )
            ->get();

        // Группировка файлов с токенами
        $groupedFiles = $files->groupBy('filename')->map(function ($group) {
            return [
                'file_id' => $group[0]->file_id,
                'filename' => $group[0]->filename,
                'tokens' => $group->pluck('token')->toArray(), // Список токенов в виде массива
                'downloadables' => $group->pluck('downloadable')->toArray(), // Список доступности
                'passwords' => $group->pluck('password')->toArray() // Список паролей
            ];
        })->values(); // Приводим к массиву


        return response()->json($groupedFiles);
    }

    public function showPasswordForm($token)
    {
        // Получаем файл из базы данных
        $filename = DB::table('links')->where('token', $token)->value('filename');
        $token_id = DB::table('links')->where('token', $token)->value('id');
        $status = DB::table('links')->where('token', $token)->value('downloadable');
        if ($token_id === null || $status === 0) {
            return redirect()->back()->withErrors(['message' => 'Файл не найден.']);
        }

        // Отправляем данные файла в представление
        return view('files.download', ['filename' => $filename, 'token_id' => $token_id]); // Изменено на 'file_link'
    }

    // Метод для обработки скачивания файла
    public function downloadFile(Request $request, $token_id)
    {
        // Получаем данные о файле
        $fileData = DB::table('links')->where('id', $token_id)->first();

        if (!$fileData) {
            return redirect()->route('profile.profile')->withErrors(['message' => 'Файл не найден.']);
        }

        // Проверяем статус downloadable
        if (!$fileData->downloadable) {
            return redirect()->route('profile.profile')->withErrors(['message' => 'Файл недоступен для скачивания.']);
        }

        // Проверяем пароль
        if ($request->input('password') !== $fileData->password) {
            return redirect()->back()->withErrors(['message' => 'Неверный пароль.']);
        }

        // Обновляем статус доступности
        DB::table('links')->where('id', $token_id)->update(['downloadable' => false]);

        return response()->download(storage_path('app/private/userFiles/' . $fileData->filename));
    }

    public function upload(Request $request)
    {
        // Валидация входящих данных
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:2048', // Максимальный размер файла 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422); // Используем стандартный код 422 для ошибок валидации
        }

        // Получаем файл из запроса
        $file = $request->file('file');

        // Проверяем, действительно ли файл загружен
        if (!$file->isValid()) {
            return response()->json([
                'error' => 'Файл недействителен.'
            ], 400);
        }

        try {
            // Генерируем имя файла
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $file->getClientOriginalName());

            // Сохраняем файл
            Storage::disk('local')->putFileAs('/userFiles/', $file, $filename);

            // Сохраняем информацию в БД
            DB::table('files')->insert([
                'user_id' => Auth::id(),
                'filename' => $filename,
            ]);

            return response()->json([
                'success' => 'Файл успешно загружен и сохранен в базе данных.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ошибка при сохранении файла: ' . $e->getMessage()
            ], 500);
        }
    }
    public function generateFileLink(Request $request)
    {
        // Получаем файл
        $userFile = DB::table('files')->where('id', $request->id)->first();

        if (!$userFile) {
            return response()->json(['error' => 'Файл не найден.'], 404);
        }

        // Генерация уникального токена
        $uniqueToken = Str::random(32);
        $password = Str::random(16);

        // Сохранение нового токена в таблицу links
        DB::table('links')->insert([
            'file_id' => $userFile->id, // Ссылка на файл
            'token' => $uniqueToken,
            'filename' => $userFile->filename,
            'downloadable' => true, // Укажите статус доступности
            'created_at' => now(),
            'password' => $password, // Или добавьте пароль, если нужно
        ]);

        return response()->json(['token' => $uniqueToken]);
    }
}
