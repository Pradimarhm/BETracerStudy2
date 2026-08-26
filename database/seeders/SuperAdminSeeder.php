<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan updateOrCreate untuk mencegah error duplikasi
        // Parameter pertama adalah kondisi pencarian (email)
        // Parameter kedua adalah data yang diupdate/dibuat
        User::updateOrCreate(
            ['email' => 'superadmin@tracer.com'],
            [
                'username' => 'superadmin',
                'password' => Hash::make('Superadmin123!'),
                'role'     => 'superadmin', // Injeksi kasta tertinggi
            ]
        );

        $this->command->info('Akun Superadmin berhasil di-generate.');
    }
}
