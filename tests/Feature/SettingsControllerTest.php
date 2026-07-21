<?php

namespace Tests\Feature;

use App\Http\Controllers\SettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    public function test_store_methods_persist_to_named_tables(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('progres')) {
            DB::getSchemaBuilder()->create('progres', function ($table) {
                $table->id();
                $table->string('nama');
                $table->string('kode')->nullable();
                $table->boolean('aktif')->default(true);
                $table->integer('urutan')->default(0);
                $table->timestamps();
            });
        }

        if (! DB::getSchemaBuilder()->hasTable('packing')) {
            DB::getSchemaBuilder()->create('packing', function ($table) {
                $table->id();
                $table->string('nama');
                $table->string('kode')->nullable();
                $table->boolean('aktif')->default(true);
                $table->integer('urutan')->default(0);
                $table->timestamps();
            });
        }

        if (! DB::getSchemaBuilder()->hasTable('ekspedisi')) {
            DB::getSchemaBuilder()->create('ekspedisi', function ($table) {
                $table->id();
                $table->string('nama');
                $table->string('kode')->nullable();
                $table->boolean('aktif')->default(true);
                $table->integer('urutan')->default(0);
                $table->timestamps();
            });
        }

        $controller = new SettingsController();

        $progressRequest = Request::create('/admin/settings/progresses', 'POST', [
            'nama' => 'Menunggu Konfirmasi',
            'kode' => 'WAIT',
            'aktif' => true,
        ]);

        $controller->storeProgress($progressRequest);

        $this->assertDatabaseHas('progres', [
            'nama' => 'Menunggu Konfirmasi',
            'kode' => 'WAIT',
            'aktif' => true,
        ]);

        $packingRequest = Request::create('/admin/settings/packings', 'POST', [
            'nama' => 'Siap Kirim',
            'kode' => 'PACK',
            'aktif' => true,
        ]);

        $controller->storePacking($packingRequest);

        $this->assertDatabaseHas('packing', [
            'nama' => 'Siap Kirim',
            'kode' => 'PACK',
            'aktif' => true,
        ]);

        $ekspedisiRequest = Request::create('/admin/settings/ekspedisis', 'POST', [
            'nama' => 'JNE',
            'kode' => 'JNE',
            'aktif' => true,
        ]);

        $controller->storeEkspedisi($ekspedisiRequest);

        $this->assertDatabaseHas('ekspedisi', [
            'nama' => 'JNE',
            'kode' => 'JNE',
            'aktif' => true,
        ]);
    }
}
