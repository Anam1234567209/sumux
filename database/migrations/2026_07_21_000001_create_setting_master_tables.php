<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['progres', 'packing', 'ekspedisi'];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) {
                    $table->id();
                    $table->string('nama');
                    $table->string('kode')->nullable();
                    $table->boolean('aktif')->default(true);
                    $table->integer('urutan')->default(0);
                    $table->timestamps();
                });
            }
        }

        if (Schema::hasTable('setting_items')) {
            $items = DB::table('setting_items')->get();

            foreach ($items as $item) {
                $targetTable = match ($item->kategori) {
                    'progres' => 'progres',
                    'packing' => 'packing',
                    'ekspedisi' => 'ekspedisi',
                    default => null,
                };

                if (! $targetTable) {
                    continue;
                }

                DB::table($targetTable)->updateOrInsert(
                    ['nama' => $item->nama, 'kode' => $item->kode],
                    [
                        'aktif' => (bool) $item->aktif,
                        'urutan' => $item->urutan ?? 0,
                        'created_at' => $item->created_at ?? now(),
                        'updated_at' => $item->updated_at ?? now(),
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        foreach (['progres', 'packing', 'ekspedisi'] as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
};
