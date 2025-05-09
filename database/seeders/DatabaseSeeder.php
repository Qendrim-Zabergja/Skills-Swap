<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\SkillSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users if they don't exist
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'profile_photo_path' => 'profile-photos/test-user.jpg'
            ]);
        }
        
        if (!User::where('email', 'jane@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'profile_photo_path' => 'profile-photos/jane-smith.jpg'
            ]);
        }

        $this->call([
            SkillSeeder::class,
        ]);
    }
}
