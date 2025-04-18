<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Design', 'Development', 'Music', 'Language', 'Business', 'Lifestyle', 'Photography', 'Education'];
        
        // Create some sample users
        $users = User::factory()->count(5)->create();
        
        foreach ($users as $user) {
            // Each user will have 2-4 skills
            $numSkills = rand(2, 4);
            
            for ($i = 0; $i < $numSkills; $i++) {
                Skill::create([
                    'user_id' => $user->id,
                    'title' => $this->getRandomSkillTitle($categories[array_rand($categories)]),
                    'description' => $this->getRandomDescription(),
                    'category' => $categories[array_rand($categories)],
                ]);
            }
        }
    }

    private function getRandomSkillTitle($category)
    {
        $skills = [
            'Design' => [
                'UI/UX Design',
                'Graphic Design',
                'Logo Design',
                'Brand Identity Design',
                'Web Design'
            ],
            'Development' => [
                'Web Development',
                'React.js',
                'Python Programming',
                'Mobile App Development',
                'Database Design'
            ],
            'Music' => [
                'Guitar Lessons',
                'Piano Basics',
                'Music Theory',
                'Vocal Training',
                'Music Production'
            ],
            'Language' => [
                'English Conversation',
                'Spanish for Beginners',
                'Japanese Language',
                'French Lessons',
                'German Language'
            ],
            'Business' => [
                'Digital Marketing',
                'Business Strategy',
                'Project Management',
                'Financial Planning',
                'Social Media Marketing'
            ],
            'Lifestyle' => [
                'Yoga',
                'Cooking',
                'Fitness Training',
                'Meditation',
                'Personal Development'
            ],
            'Photography' => [
                'Portrait Photography',
                'Landscape Photography',
                'Photo Editing',
                'Mobile Photography',
                'Studio Lighting'
            ],
            'Education' => [
                'Math Tutoring',
                'Science Education',
                'Academic Writing',
                'Test Preparation',
                'Study Skills'
            ]
        ];

        return $skills[$category][array_rand($skills[$category])];
    }

    private function getRandomDescription()
    {
        $descriptions = [
            "I'll help you master the fundamentals and advanced concepts through hands-on practice and real-world examples.",
            "Learn from an experienced professional with years of practical experience in the field.",
            "From beginner to advanced level, I'll guide you through a structured learning path.",
            "Practical, project-based learning approach to help you gain real-world skills.",
            "One-on-one sessions tailored to your learning pace and goals.",
            "Comprehensive training covering both theory and practical applications.",
            "Learn industry best practices and professional techniques.",
            "Interactive sessions with immediate feedback and guidance.",
            "Flexible learning schedule with personalized attention.",
            "Build a strong foundation with systematic learning approach."
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
