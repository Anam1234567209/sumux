<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankCustomerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bank_customer')->insert([

            ['nama_bank' => 'Bank BRI', 'kode_bank' => 'BRI'],
            ['nama_bank' => 'Bank BCA', 'kode_bank' => 'BCA'],
            ['nama_bank' => 'Bank Mandiri', 'kode_bank' => 'MANDIRI'],
            ['nama_bank' => 'Bank BNI', 'kode_bank' => 'BNI'],
            ['nama_bank' => 'Bank BTN', 'kode_bank' => 'BTN'],
            ['nama_bank' => 'Bank CIMB Niaga', 'kode_bank' => 'CIMB'],
            ['nama_bank' => 'Bank Permata', 'kode_bank' => 'PERMATA'],
            ['nama_bank' => 'Bank Danamon', 'kode_bank' => 'DANAMON'],
            ['nama_bank' => 'Bank Mega', 'kode_bank' => 'MEGA'],
            ['nama_bank' => 'SeaBank', 'kode_bank' => 'SEABANK'],
            ['nama_bank' => 'Bank Jago', 'kode_bank' => 'JAGO'],
            ['nama_bank' => 'Bank Neo Commerce', 'kode_bank' => 'BNC'],

            ['nama_bank' => 'DANA', 'kode_bank' => 'DANA'],
            ['nama_bank' => 'OVO', 'kode_bank' => 'OVO'],
            ['nama_bank' => 'GoPay', 'kode_bank' => 'GOPAY'],
            ['nama_bank' => 'ShopeePay', 'kode_bank' => 'SHOPEEPAY'],

            ['nama_bank' => 'QRIS', 'kode_bank' => 'QRIS'],

            ['nama_bank' => 'Tunai', 'kode_bank' => 'CASH'],
        ]);
    }
}
