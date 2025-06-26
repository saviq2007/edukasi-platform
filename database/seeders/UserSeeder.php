<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus user yang sudah ada (jika ada)
        User::where('email', 'admin@edukasi.com')->delete();
        User::where('email', 'user@edukasi.com')->delete();

        // Buat akun Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@edukasi.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Buat akun User
        User::create([
            'name' => 'User Demo',
            'email' => 'user@edukasi.com',
            'password' => Hash::make('user123'),
            'role' => 'user'
        ]);

        $this->command->info('✅ Akun berhasil dibuat:');
        $this->command->info('👨‍💼 Admin: admin@edukasi.com / admin123');
        $this->command->info('👤 User: user@edukasi.com / user123');
    }
}
