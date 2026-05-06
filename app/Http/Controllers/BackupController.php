<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
   use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class BackupController extends Controller
{
    public function downloadBackup()
    {
        try {
            // ✅ Allow long-running process
            ini_set('max_execution_time', 900); // 15 mins
            ini_set('memory_limit', '2048M');
            set_time_limit(0);
            ignore_user_abort(true);

            // ==== 1. Prepare paths ====
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $baseName = preg_replace('/[^A-Za-z0-9_\-]/', '_', config('app.name') ?: 'project_code_backup');
            $zipFilename = "{$baseName}_{$timestamp}.zip";
            $zipFilepath = "{$backupDir}/{$zipFilename}";

            // ==== 2. Create temporary SQL dump ====
            $dbDumpPath = "{$backupDir}/database_{$timestamp}.sql";
            $this->createDatabaseBackup($dbDumpPath);

            // ==== 3. Create ZIP file ====
            $zip = new ZipArchive();
            if ($zip->open($zipFilepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

                $rootPath = base_path();
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($files as $file) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);

                    // 🚫 Skip unnecessary folders
                    if (preg_match('/^(vendor|node_modules|storage\/backups|storage\/logs|storage\/framework|public\/uploads)/', $relativePath)) {
                        continue;
                    }

                    if ($file->isDir()) {
                        $zip->addEmptyDir($relativePath);
                    } else {
                        $zip->addFile($filePath, $relativePath);
                    }
                }

                // ✅ Add SQL dump file
                if (file_exists($dbDumpPath)) {
                    $zip->addFile($dbDumpPath, 'database.sql');
                }

                $zip->close();
            } else {
                throw new \Exception('Could not create ZIP archive.');
            }

            // ==== 4. Delete SQL dump after zipping ====
            if (file_exists($dbDumpPath)) {
                unlink($dbDumpPath);
            }

            // ==== 5. Force download ====
            return Response::download($zipFilepath, $zipFilename, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Create SQL dump of current database
     */

protected function createDatabaseBackup(string $outputPath)
{
    $connection = config('database.default');
    $dbConfig = config("database.connections.{$connection}");

    $host = $dbConfig['host'] ?? '127.0.0.1';
    $port = $dbConfig['port'] ?? 3306;
    $database = $dbConfig['database'] ?? '';
    $username = $dbConfig['username'] ?? '';
    $password = $dbConfig['password'] ?? '';

    // ✅ Build mysqldump command
    $command = [
        'mysqldump',
        "--user={$username}",
        "--password={$password}",
        "--host={$host}",
        "--port={$port}",
        $database,
    ];

    // Run the command using Symfony Process
    $process = new Process($command);
    $process->run();

    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }

    // ✅ Save SQL output
    file_put_contents($outputPath, $process->getOutput());
}

}
