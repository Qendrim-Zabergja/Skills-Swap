<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;
use App\Models\User;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            [
                'name' => 'Web Development',
                'category' => 'Development',
                'description' => 'Full-stack web development including frontend and backend',
            ],
            [
                'name' => 'Digital Photography',
                'category' => 'Photography',
                'description' => 'Professional photography techniques and editing',
            ],
            [
                'name' => 'Guitar',
                'category' => 'Music',
                'description' => 'Acoustic and electric guitar lessons',
            ],
            [
                'name' => 'Spanish Language',
                'category' => 'Language',
                'description' => 'Spanish language lessons for beginners to advanced',
            ],
            [
                'name' => 'Digital Marketing',
                'category' => 'Business',
                'description' => 'Social media marketing and SEO strategies',
            ],
            [
                'name' => 'Yoga',
                'category' => 'Lifestyle',
                'description' => 'Yoga practice and meditation techniques',
            ],
            [
                'name' => 'UI/UX Design',
                'category' => 'Design',
                'description' => 'User interface and experience design principles',
            ],
            [
                'name' => 'Mathematics Tutoring',
                'category' => 'Education',
                'description' => 'Math tutoring for high school and college students',
            ]
        ];

        // Get the test user
        $user = User::where('email', 'test@example.com')->first();
        
        if ($user) {
            foreach ($skills as $skill) {
                $skill['user_id'] = $user->id;
                Skill::create($skill);
            }
        }
    }
}
