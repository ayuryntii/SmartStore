<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanController extends Controller
{
    public function index()
    {
        $dbName = config('database.connections.mysql.database');
        $dbHost = config('database.connections.mysql.host');
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();

        $mariaDbVersion = '10.4.32-MariaDB';
        try {
            $versionResult = DB::select('SELECT VERSION() as ver');
            if (!empty($versionResult)) {
                $mariaDbVersion = $versionResult[0]->ver;
            }
        } catch (\Exception $e) {
            // fallback
        }

        return view('pengaturan.index', compact(
            'dbName',
            'dbHost',
            'phpVersion',
            'laravelVersion',
            'mariaDbVersion'
        ));
    }

    public function downloadBackup()
    {
        $sqlPath = base_path('bawara.sql');
        if (file_exists($sqlPath)) {
            return response()->download($sqlPath, 'bawara_backup_' . date('Ymd_His') . '.sql');
        }

        return back()->with('error', 'File skema database bawara.sql tidak ditemukan.');
    }
}
