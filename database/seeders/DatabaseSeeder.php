<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create(array(
            'id' => rand(100, 500),
            'uuid' => 'e4ea84b9-237e-4d58-ba5c-181acbb1f381',
            'name' => 'Admin Marmud',
            'role' => 1,
            'email' => 'admin@gmail.com',
            'email_verified_at' => now()->toTimeString(),
            'password' => Hash::make('admin123'),
        ));
    }
}
