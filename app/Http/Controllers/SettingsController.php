<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    public function index()
    {
        $banks = Schema::hasTable('bank_customer')
            ? DB::table('bank_customer')
            ->orderByDesc('aktif')
            ->orderBy('nama_bank')
            ->get()
            : collect();

        $progresses = Schema::hasTable('progres')
            ? DB::table('progres')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get()
            : collect();

        $packings = Schema::hasTable('packing')
            ? DB::table('packing')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get()
            : collect();

        $ekspedisis = Schema::hasTable('ekspedisi')
            ? DB::table('ekspedisi')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get()
            : collect();

        return view('admin.settings', compact('banks', 'progresses', 'packings', 'ekspedisis'));
    }

    public function storeBank(Request $request)
    {
        $data = $request->validate([
            'nama_bank' => ['required', 'string', 'max:100'],
            'kode_bank' => ['required', 'string', 'max:20', 'unique:bank_customer,kode_bank'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'nama_bank.required' => 'Nama bank wajib diisi.',
            'kode_bank.required' => 'Kode bank wajib diisi.',
            'kode_bank.unique' => 'Kode bank sudah digunakan.',
        ]);

        DB::table('bank_customer')->insert([
            'nama_bank' => $data['nama_bank'],
            'kode_bank' => strtoupper($data['kode_bank']),
            'aktif' => $request->boolean('aktif', true),
            'dibuat_pada' => now(),
            'diperbarui_pada' => now(),
        ]);

        return redirect()->route('admin.settings')->with('success', 'Data bank berhasil ditambahkan.');
    }

    public function storeProgress(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kode' => ['nullable', 'string', 'max:30'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'nama.required' => 'Nama progres wajib diisi.',
        ]);

        DB::table('progres')->insert([
            'nama' => $data['nama'],
            'kode' => $data['kode'] ? strtoupper($data['kode']) : null,
            'aktif' => $request->boolean('aktif', true),
            'urutan' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.settings')->with('success', 'Data progres berhasil ditambahkan.');
    }

    public function storePacking(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kode' => ['nullable', 'string', 'max:30'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'nama.required' => 'Nama packing wajib diisi.',
        ]);

        DB::table('packing')->insert([
            'nama' => $data['nama'],
            'kode' => $data['kode'] ? strtoupper($data['kode']) : null,
            'aktif' => $request->boolean('aktif', true),
            'urutan' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.settings')->with('success', 'Data packing berhasil ditambahkan.');
    }

    public function destroyPacking(int $id)
    {
        DB::table('packing')->where('id', $id)->delete();

        return redirect()->route('admin.settings')->with('success', 'Data packing berhasil dihapus.');
    }

    public function storeEkspedisi(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kode' => ['nullable', 'string', 'max:30'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'nama.required' => 'Nama ekspedisi wajib diisi.',
        ]);

        DB::table('ekspedisi')->insert([
            'nama' => $data['nama'],
            'kode' => $data['kode'] ? strtoupper($data['kode']) : null,
            'aktif' => $request->boolean('aktif', true),
            'urutan' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.settings')->with('success', 'Data ekspedisi berhasil ditambahkan.');
    }
}
