<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    public function index()
    {
        try {
            $backups = $this->getBackups();
            return view('backups.index', compact('backups'));
        } catch (\Exception $e) {
            Log::error('Error al cargar la vista de backups: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la página de backups: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            Artisan::call('system:backup');
            return back()->with('success', 'Backup creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear backup: ' . $e->getMessage());
            return back()->with('error', 'Error al crear el backup: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        try {
            $path = storage_path('app/backups/' . $filename);
            if (!file_exists($path)) {
                return back()->with('error', 'El archivo de backup no existe');
            }
            return response()->download($path);
        } catch (\Exception $e) {
            Log::error('Error al descargar backup: ' . $e->getMessage());
            return back()->with('error', 'Error al descargar el backup: ' . $e->getMessage());
        }
    }

    public function destroy($filename)
    {
        try {
            $path = storage_path('app/backups/' . $filename);
            if (!file_exists($path)) {
                return back()->with('error', 'El archivo de backup no existe');
            }
            unlink($path);
            return back()->with('success', 'Backup eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar backup: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el backup: ' . $e->getMessage());
        }
    }

    public function restore($filename)
    {
        try {
            $path = storage_path('app/backups/' . $filename);
            if (!file_exists($path)) {
                return back()->with('error', 'El archivo de backup no existe');
            }

            // Ejecutar el comando de restauración
            $type = strpos($filename, 'db_') !== false ? 'db' : 'files';
            Artisan::call('system:restore', [
                'filename' => $filename,
                '--type' => $type
            ]);

            return back()->with('success', 'Backup restaurado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al restaurar backup: ' . $e->getMessage());
            return back()->with('error', 'Error al restaurar el backup: ' . $e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'backup_file' => 'required|file|mimes:zip|max:102400' // máximo 100MB
            ]);

            $file = $request->file('backup_file');
            $filename = $file->getClientOriginalName();
            
            // Verificar que el nombre del archivo sea válido
            if (!preg_match('/^(db_|files_)/', $filename)) {
                return back()->with('error', 'El archivo debe ser un backup válido (debe comenzar con db_ o files_)');
            }

            // Mover el archivo a la carpeta de backups
            $file->move(storage_path('app/backups'), $filename);

            return back()->with('success', 'Backup subido exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al subir backup: ' . $e->getMessage());
            return back()->with('error', 'Error al subir el backup: ' . $e->getMessage());
        }
    }

    private function getBackups()
    {
        $backups = [];
        $files = glob(storage_path('app/backups/*.zip'));

        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'type' => strpos($file, 'db_') !== false ? 'Base de datos' : 'Archivos',
                'size' => $this->formatSize(filesize($file)),
                'date' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }

        usort($backups, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $backups;
    }

    private function formatSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
} 