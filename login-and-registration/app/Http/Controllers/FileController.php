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
        $filename = DB::table('link_files')->where('token', $token)->value('filename');
        $token_id = DB::table('link_files')->where('token', $token)->value('id');
        $status = DB::table('link_files')->where('token', $token)->value('downloadable');
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
        $fileData = DB::table('link_files')->where('id', $token_id)->first();

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

        // Скачиваем файл
        $filePath = public_path('userFiles/' . $fileData->filename);

        // Обновляем статус доступности
        DB::table('link_files')->where('id', $token_id)->update(['downloadable' => false]);

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
