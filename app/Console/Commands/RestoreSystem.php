<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class RestoreSystem extends Command
{
    protected $signature = 'system:restore {filename : Nombre del archivo de backup} {--type=all : Tipo de backup a restaurar (all, db, files)}';
    protected $description = 'Restaura el sistema desde un backup';

    public function handle()
    {
        $filename = $this->argument('filename');
        $type = $this->option('type');
        $backupPath = storage_path('app/backups/' . $filename);

        if (!file_exists($backupPath)) {
            $this->error('El archivo de backup no existe');
            return 1;
        }

        try {
            if ($type === 'all' || $type === 'db') {
                if (strpos($filename, 'db_') !== false) {
                    $this->restoreDatabase($backupPath);
                }
            }

            if ($type === 'all' || $type === 'files') {
                if (strpos($filename, 'files_') !== false) {
                    $this->restoreFiles($backupPath);
                }
            }

            $this->info('Restauración completada exitosamente');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error durante la restauración: ' . $e->getMessage());
            return 1;
        }
    }

    private function restoreDatabase($backupPath)
    {
        $this->info('Restaurando base de datos...');
        
        // Crear directorio temporal
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Extraer el archivo ZIP
        $zip = new ZipArchive();
        if ($zip->open($backupPath) === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();
        }

        // Encontrar el archivo SQL
        $sqlFile = glob($tempDir . '/*.sql')[0] ?? null;
        if (!$sqlFile) {
            throw new \Exception('No se encontró el archivo SQL en el backup');
        }

        // Leer y ejecutar el SQL
        $sql = file_get_contents($sqlFile);
        DB::unprepared($sql);

        // Limpiar archivos temporales
        array_map('unlink', glob($tempDir . '/*.*'));
        rmdir($tempDir);

        $this->info('Base de datos restaurada exitosamente');
    }

    private function restoreFiles($backupPath)
    {
        $this->info('Restaurando archivos...');

        // Crear directorio temporal
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Extraer el archivo ZIP
        $zip = new ZipArchive();
        if ($zip->open($backupPath) === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();
        }

        // Restaurar archivos públicos
        if (file_exists($tempDir . '/public')) {
            $this->copyDirectory($tempDir . '/public', storage_path('app/public'));
        }

        // Restaurar uploads
        if (file_exists($tempDir . '/uploads')) {
            $this->copyDirectory($tempDir . '/uploads', public_path('uploads'));
        }

        // Limpiar archivos temporales
        $this->deleteDirectory($tempDir);

        $this->info('Archivos restaurados exitosamente');
    }

    private function copyDirectory($source, $destination)
    {
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $src = $source . '/' . $file;
                $dst = $destination . '/' . $file;

                if (is_dir($src)) {
                    $this->copyDirectory($src, $dst);
                } else {
                    copy($src, $dst);
                }
            }
        }
        closedir($dir);
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
} 