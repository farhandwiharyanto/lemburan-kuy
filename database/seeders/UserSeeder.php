<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Hapus data yang ada (gunakan delete instead of truncate)
        User::query()->delete();
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@lemburankuy.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => 'IT'
        ]);

        // Create Pimpinan Users
        User::create([
            'name' => 'Manager IT',
            'email' => 'pimpinan@lemburankuy.com',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
            'department' => 'IT'
        ]);

        User::create([
            'name' => 'Manager HRD',
            'email' => 'hrd@lemburankuy.com',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
            'department' => 'HRD'
        ]);

        User::create([
            'name' => 'Manager Finance',
            'email' => 'finance@lemburankuy.com',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
            'department' => 'Finance'
        ]);

        // Create Bawahan Users
        User::create([
            'name' => 'Karyawan IT 1',
            'email' => 'bawahan@lemburankuy.com',
            'password' => Hash::make('password'),
            'role' => 'bawahan',
            'department' => 'IT'
        ]);

        User::create([
            'name' => 'Karyawan IT 2',
            'email' => 'it2@lemburankuy.com',
            'password' => Hash::make('password'),
            'role' => 'bawahan',
            'department' => 'IT'
        ]);

        User::create([
            'name' => 'Karyawan HRD',
            'email' => 'hrdstaff@lemburankuy.com',
            'password' => Hash::make('password'),
            'role' => 'bawahan',
            'department' => 'HRD'
        ]);

        User::create([
            'name' => 'Karyawan Finance',
            'email' => 'financestaff@lemburankuy.com',
            'password' => Hash::make('password'),
            'role' => 'bawahan',
            'department' => 'Finance'
        ]);

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin: admin@lemburankuy.com / password');
        $this->command->info('Pimpinan: pimpinan@lemburankuy.com / password');
        $this->command->info('Bawahan: bawahan@lemburankuy.com / password');
    }
}