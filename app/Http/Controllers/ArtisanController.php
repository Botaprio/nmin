<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ArtisanController extends Controller
{
    public function index()
    {
        return view('artisan.index');
    }

    public function makeMigration(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $name = $request->input('name');
            $table = $request->input('table');
            $create = $request->boolean('create');

            $command = 'make:migration';
            $parameters = ['name' => $name];

            if ($table) {
                $parameters['--table'] = $table;
            }

            if ($create) {
                $parameters['--create'] = $table ?: $name;
            }

            Artisan::call($command, $parameters);

            $output = Artisan::output();

            // Buscar el archivo creado
            $migrationPath = database_path('migrations');
            $files = File::files($migrationPath);
            $latestFile = collect($files)->sortByDesc(function ($file) {
                return $file->getCTime();
            })->first();

            return response()->json([
                'success' => true,
                'message' => 'Migración creada exitosamente',
                'output' => $output,
                'file' => $latestFile ? $latestFile->getFilename() : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la migración: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function makeSeeder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $name = $request->input('name');
            Artisan::call('make:seeder', ['name' => $name]);

            $output = Artisan::output();

            // Buscar el archivo creado
            $seederPath = database_path('seeders');
            $files = File::files($seederPath);
            $latestFile = collect($files)->sortByDesc(function ($file) {
                return $file->getCTime();
            })->first();

            return response()->json([
                'success' => true,
                'message' => 'Seeder creado exitosamente',
                'output' => $output,
                'file' => $latestFile ? $latestFile->getFilename() : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el seeder: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function runMigrations(Request $request)
    {
        try {
            $fresh = $request->boolean('fresh');
            $seed = $request->boolean('seed');

            if ($fresh) {
                Artisan::call('migrate:fresh', $seed ? ['--seed' => true] : []);
            } else {
                Artisan::call('migrate', $seed ? ['--seed' => true] : []);
            }

            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Migraciones ejecutadas exitosamente',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al ejecutar migraciones: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function runSeeders(Request $request)
    {
        try {
            $class = $request->input('class');

            if ($class) {
                Artisan::call('db:seed', ['--class' => $class]);
            } else {
                Artisan::call('db:seed');
            }

            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Seeders ejecutados exitosamente',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al ejecutar seeders: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function listMigrations()
    {
        try {
            $migrations = DB::table('migrations')->orderBy('batch', 'desc')->orderBy('migration', 'desc')->get();

            return response()->json([
                'success' => true,
                'migrations' => $migrations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al listar migraciones: ' . $e->getMessage(),
            ], 500);
        }
    }
}