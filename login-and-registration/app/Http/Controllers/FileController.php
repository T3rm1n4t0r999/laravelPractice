<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function Laravel\Prompts\password;

class FileController extends Controller
{
    public function getUserFiles(Request $request)
    {
        // Получаем файлы пользователя и объединяем с токенами
        $files = DB::table('user_files as uf')
            ->leftJoin('link_files as lf', 'uf.id', '=', 'lf.user_file_id')
            ->where('uf.user_id', Auth::id())
            ->select(
                'uf.id as user_file_id',
                'uf.filename',
                'lf.token',
                'lf.downloadable',
                'lf.password'
            )
            ->get();

        // Группировка файлов с токенами
        $groupedFiles = $files->groupBy('filename')->map(function ($group) {
            return [
                'user_file_id' => $group[0]->user_file_id,
                'filename' => $group[0]->filename,
                'tokens' => $group->pluck('token')->filter()->toArray(), // Список токенов в виде массива
                'downloadables' => $group->pluck('downloadable')->filter()->toArray(), // Список доступности
                'passwords' => $group->pluck('password')->filter()->toArray() // Список паролей
            ];
        })->values(); // Приводим к массиву

        // Обработка данных для обеспечения корректного формата
        foreach ($groupedFiles as &$file) {
            // Если массив токенов пуст, задаем значение по умолчанию
            if (empty($file['tokens'])) {
                $file['tokens'] = ['Нет токенов'];
            }

            // Если массив доступности пуст, задаем значение по умолчанию
            if (empty($file['downloadables'])) {
                $file['downloadables'] = ['Нет доступности'];
            }

            // Если массив паролей пуст, задаем значение по умолчанию
            if (empty($file['passwords'])) {
                $file['passwords'] = ['Нет паролей'];
            }
        }

        return response()->json($groupedFiles);
    }

    public function showPasswordForm($token)
    {
        // Получаем файл из базы данных
        $filename = DB::table('link_files')->where('token', $token)->value('filename');
        $token_id = DB::table('link_files')->where('token', $token)->value('id');
        if (!$filename) {
            return redirect()->back()->withErrors(['message' => 'Файл не найден.']);
        }

        // Отправляем данные файла в представление
        return view('files.download', ['filename' => $filename, 'token_id' => $token_id]); // Изменено на 'file_link'
    }

    // Метод для обработки скачивания файла
    public function downloadFile(Request $request, $token_id)
    {
        $filename = DB::table('link_files')->where('id', $token_id)->value('filename');

        if (!$filename) {
            return redirect()->back()->withErrors(['message' => 'Файл не найден.']);
        }

        $status = DB::table('link_files')->where('id', $token_id)->value('downloadable');
        $pass = DB::table('link_files')->where('id', $token_id)->value('password');
        // Проверяем статус downloadable
        if ($status != true) {
            return redirect()->back()->withErrors(['message' => 'Файл недоступен для скачивания.']);
        }

        // Проверяем пароль
        if ($request->input('password') !== $pass) {
            return redirect()->back()->withErrors(['message' => 'Неверный пароль.']);
        }

        // Скачиваем файл
        $filePath = public_path('userFiles/' . $filename);

        DB::table('link_files')->where('id', $token_id)->delete();

        return response()->download($filePath);
    }

    public function upload(Request $request)
    {
        // Валидация входящих данных
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:2048', // Максимальный размер файла 2MB
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        // Получаем файл из запроса
        $file = $request->file('file');

        // Проверяем, действительно ли файл загружен
        if (!$file->isValid()) {
            return response()->json(['error' => 'Файл недействителен.'], 400);
        }

        // Генерируем имя файла
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $file->getClientOriginalName());

        // Указываем жесткий путь для сохранения файла
        $hardPath = public_path('userFiles'); // Полный путь к директории в public

        // Сохраняем файл в указанную директорию
        $file->move($hardPath, $filename); // Сохраняем файл

        // Формируем путь к файлу
        $fileLink = "files/" . $filename; // Получаем URL файла

        $randomPassword = Str::random(16); // Генерируем случайный пароль длиной 16 символов

        // Добавляем запись в базу данных
        try {
            DB::table('user_files')->insert([
                'user_id' => Auth::id(),
                'filename' => $filename,
                'file_link' => $fileLink, // Используем URL файла
            ]);

            return response()->json(['success' => 'Файл успешно загружен и сохранен в базе данных.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка при сохранении файла в базе данных: ' . $e->getMessage()], 500);
        }
    }
    public function generateFileLink(Request $request)
    {

        // Валидация входящих данных
//        $request->validate([
//            'id' => 'required|exists:user_files,id', // Убедитесь, что файл существует
//        ]);

        // Получаем файл
        $userFile = DB::table('user_files')->where('id', $request->id)->first();

        if (!$userFile) {
            return response()->json(['error' => 'Файл не найден.'], 404);
        }

        // Генерация уникального токена
        $uniqueToken = Str::random(32);
        $password = Str::random(16);

        // Сохранение нового токена в таблицу link_files
        DB::table('link_files')->insert([
            'user_file_id' => $userFile->id, // Ссылка на файл
            'token' => $uniqueToken,
            'filename' => $userFile->filename,
            'downloadable' => true, // Укажите статус доступности
            'created_at' => now(),
            'password' => $password, // Или добавьте пароль, если нужно
        ]);

        return response()->json(['token' => $uniqueToken]);
    }
}
