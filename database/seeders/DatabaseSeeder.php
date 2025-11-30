<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user with known credentials
        $testUser = User::factory()->create([
            'username' => 'testuser',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'bio' => 'This is a test account for development.',
        ]);

        // Create posts for the test user
        Post::factory(5)->create([
            'user_id' => $testUser->id,
        ]);

        // Create additional users with posts
        User::factory(10)->create()->each(function ($user) {
            Post::factory(rand(1, 5))->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
