<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackupSystem extends Command
{
    protected $signature = 'system:backup {--type=all : Tipo de backup (all, db, files)}';
    protected $description = 'Realiza backup del sistema (base de datos y archivos)';

    public function handle()
    {
        $type = $this->option('type');
        $date = Carbon::now()->format('Y-m-d_H-i-s');
        $backupPath = storage_path('app/backups');
        
        // Crear directorio de backups si no existe
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        try {
            if ($type === 'all' || $type === 'db') {
                $this->backupDatabase($date);
            }

            if ($type === 'all' || $type === 'files') {
                $this->backupFiles($date);
            }

            // Limpiar backups antiguos (mantener solo los últimos 7 días)
            $this->cleanOldBackups();

            $this->info('Backup completado exitosamente');
            Log::info('Backup del sistema completado', ['date' => $date, 'type' => $type]);
        } catch (\Exception $e) {
            $this->error('Error durante el backup: ' . $e->getMessage());
            Log::error('Error en backup del sistema', [
                'error' => $e->getMessage(),
                'date' => $date,
                'type' => $type
            ]);
        }
    }

    protected function backupDatabase($date)
    {
        $this->info('Iniciando backup de la base de datos...');
        
        $filename = "db_backup_{$date}.sql";
        $filepath = storage_path("app/backups/{$filename}");
        
        // Obtener configuración de la base de datos
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        // Comando para mysqldump
        $command = sprintf(
            'mysqldump --host=%s --user=%s --password=%s %s > %s',
            $host,
            $username,
            $password,
            $database,
            $filepath
        );

        // Ejecutar el comando
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Error al realizar el backup de la base de datos');
        }

        // Comprimir el archivo SQL
        $zip = new \ZipArchive();
        $zipName = storage_path("app/backups/db_backup_{$date}.zip");
        
        if ($zip->open($zipName, \ZipArchive::CREATE) === TRUE) {
            $zip->addFile($filepath, $filename);
            $zip->close();
            unlink($filepath); // Eliminar el archivo SQL original
        }

        $this->info('Backup de la base de datos completado');
    }

    protected function backupFiles($date)
    {
        $this->info('Iniciando backup de archivos...');
        
        $zip = new \ZipArchive();
        $zipName = storage_path("app/backups/files_backup_{$date}.zip");
        
        if ($zip->open($zipName, \ZipArchive::CREATE) === TRUE) {
            // Backup de archivos de storage/app/public
            $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage');
            // Backup de archivos de storage/app/private
            $this->addDirectoryToZip($zip, storage_path('app/private'), 'private');
            // Backup de public/images
            $this->addDirectoryToZip($zip, public_path('images'), 'public_images');
            // Backup de public/build
            $this->addDirectoryToZip($zip, public_path('build'), 'public_build');
            // Puedes agregar más carpetas aquí si lo necesitas
            $zip->close();
        }

        $this->info('Backup de archivos completado');
    }

    protected function addDirectoryToZip($zip, $path, $relativePath)
    {
        if (!file_exists($path)) {
            return;
        }
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                // Evitar incluir archivos de backups dentro del backup
                if (strpos($filePath, storage_path('app/backups')) === 0) {
                    continue;
                }
                $localRelativePath = $relativePath . '/' . substr($filePath, strlen($path) + 1);
                $zip->addFile($filePath, $localRelativePath);
            }
        }
    }

    protected function cleanOldBackups()
    {
        $this->info('Limpiando backups antiguos...');
        
        $files = glob(storage_path('app/backups/*'));
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 7 * 24 * 60 * 60) { // 7 días
                    unlink($file);
                }
            }
        }
    }
} 