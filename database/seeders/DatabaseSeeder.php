<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Menjalankan seeder untuk mengisi database dengan data awal
     * Membuat admin dan demo user beserta sample data
     */
    public function run(): void
    {
        // Membuat atau update user admin dari config dengan fallback values
        $adminUser = config('todolist.default_users.admin', [
            'name' => 'Admin User',
            'email' => 'admin@todolist.com',
            'role' => 'admin'
        ]);
        
        // Pastikan config admin user valid
        if (!$adminUser || !isset($adminUser['email'], $adminUser['name'], $adminUser['role'])) {
            throw new \Exception('Invalid admin user configuration in todolist.default_users.admin');
        }
        
        $defaultPassword = config('todolist.defaults.default_password', 'password');
        
        User::updateOrCreate(
            ['email' => $adminUser['email']],
            [
                'name' => $adminUser['name'],
                'role' => $adminUser['role'],
                'password' => bcrypt($defaultPassword),
                'email_verified_at' => now()
            ]
        );

        // Membuat atau update user demo untuk testing dari config dengan fallback values
        $demoUser = config('todolist.default_users.demo', [
            'name' => 'Demo User',
            'email' => 'demo@todolist.com',
            'role' => 'user'
        ]);
        
        // Pastikan config demo user valid
        if (!$demoUser || !isset($demoUser['email'], $demoUser['name'], $demoUser['role'])) {
            throw new \Exception('Invalid demo user configuration in todolist.default_users.demo');
        }
        
        User::updateOrCreate(
            ['email' => $demoUser['email']],
            [
                'name' => $demoUser['name'],
                'role' => $demoUser['role'],
                'password' => bcrypt($defaultPassword),
                'email_verified_at' => now()
            ]
        );

        // Seed categories and tasks
        $this->call([
            CategorySeeder::class,
            TaskSeeder::class,
        ]);
    }
}
