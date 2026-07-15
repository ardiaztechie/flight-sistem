<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@garuda.com'],
            [
                'name'     => 'Admin Garuda',
                'password' => Hash::make('admin123'),
            ]
        );

        $this->command->info('✅ Admin berhasil dibuat!');
        $this->command->line('   Email    : admin@garuda.com');
        $this->command->line('   Password : admin123');
        $this->command->line('   URL      : http://localhost:8000/admin');
    }
}
