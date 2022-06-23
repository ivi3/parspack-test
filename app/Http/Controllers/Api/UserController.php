<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Packages\Process\LinuxProcess;
use App\Packages\Zip\Zip;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function runningProcess()
    {
        $process = resolve(LinuxProcess::class);
        return $process->getCurrentProcesses();
    }

    public function createDirectory($path): JsonResponse
    {
        try {
            $username = Auth::user()->username;
            $path = config("filesystems.user_data_path") ."/$username/$path";
            $this->makeDirectory($path);
            return response()->json(['message' => 'successfully created!']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 406);
        }
    }

    public function createFile($name): JsonResponse
    {
        $path = config("filesystems.user_data_path") . Auth::user()->username;
        $file_path  = "$path/$name";
        $this->makeDirectory($path);
        try {
            File::put($file_path, "");
            return response()->json(['message' => 'successfully created!']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 406);
        }
    }

    public function listOfDirectories(): JsonResponse
    {
        $username = Auth::user()->username;
        try {
            $dirs = Storage::disk("opt")->allDirectories($username);
            return response()->json([
                'username' => $username,
                'user_dirs' => $dirs
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 406);
        }
    }

    public function listOfFiles(): JsonResponse
    {
        $username = Auth::user()->username;
        try {
            $dirs = Storage::disk("opt")->allFiles($username);
            return response()->json([
                'username' => $username,
                'user_files' => $dirs
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 406);
        }
    }

    public function usersBackupData()
    {
        // vars
        $y_m_d_date = Carbon::now()->format("Y-m-d");
        $zipPath = config("filesystems.backup_path");
        $userDataPath = config("filesystems.user_data_path");

        $zip = new Zip();
        $directories = Storage::disk('opt')->allDirectories("/");
        collect($directories)
            ->map(fn($item) => explode('/', $item))
            ->filter(fn($item) => count($item) == 1)
            ->values()
            ->flatten()
            ->each(function ($username_folder_name) use ($zip, $zipPath, $y_m_d_date, $userDataPath) {
                $zipPath .= "/$username_folder_name";
                $userDataPath .= "/$username_folder_name";
                // make backup directory by username
                $this->makeDirectory($zipPath);
                // zip folder
                $zip->zipFolder("$zipPath/$y_m_d_date.zip", $userDataPath);
            });
    }

    private function makeDirectory($path)
    {
        if (!File::isDirectory($path))
            File::makeDirectory($path, recursive: true);
    }
}
