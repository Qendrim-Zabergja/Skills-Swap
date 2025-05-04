<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Skill;

class SkillsSeeder extends Seeder
{
    public function run(): void
    {
        // Create a user for development skills
        $devUser = User::create([
            'name' => 'John Developer',
            'email' => 'john.dev@example.com',
            'password' => bcrypt('password'),
            'location' => 'San Francisco, USA'
        ]);

        // Add development skills
        Skill::create([
            'user_id' => $devUser->id,
            'name' => 'Full-Stack Web Development',
            'description' => 'Expert in PHP, Laravel, React, and Vue.js. Can teach modern web development practices.',
            'type' => 'teach',
            'category' => 'Development'
        ]);

        Skill::create([
            'user_id' => $devUser->id,
            'name' => 'Mobile App Development',
            'description' => 'iOS and Android development using React Native and Flutter',
            'type' => 'teach',
            'category' => 'Development'
        ]);

        // Create another user for more dev skills
        $webUser = User::create([
            'name' => 'Sarah Coder',
            'email' => 'sarah.code@example.com',
            'password' => bcrypt('password'),
            'location' => 'New York, USA'
        ]);

        Skill::create([
            'user_id' => $webUser->id,
            'name' => 'Frontend Development',
            'description' => 'HTML, CSS, JavaScript, and modern frameworks. Specializing in responsive design.',
            'type' => 'teach',
            'category' => 'Development'
        ]);

        Skill::create([
            'user_id' => $webUser->id,
            'name' => 'Backend Development',
            'description' => 'PHP, Node.js, and Python development with focus on API design and database optimization',
            'type' => 'teach',
            'category' => 'Development'
        ]);
    }
}
