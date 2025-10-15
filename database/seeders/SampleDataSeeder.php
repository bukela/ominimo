<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure Admin user exists with known credentials
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // Ensure Moderator user exists with known credentials
        $moderator = User::updateOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Moderator User',
                'role' => User::ROLE_MODERATOR,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // Additional sample users
        $users = User::factory(8)->create();

        $tags = Tag::factory(10)->create();

        $allUsers = collect([$admin, $moderator])->merge($users);

        Post::factory(25)->create()->each(function (Post $post) use ($allUsers, $tags) {
            $post->user()->associate($allUsers->random())->save();

            // Attach 0-3 random tags
            $post->tags()->sync($tags->random(random_int(0, 3))->pluck('id')->all());

            // Add comments
            Comment::factory(random_int(0, 5))->create([
                'post_id' => $post->id,
                'user_id' => $allUsers->random()->id,
            ]);
        });
    }
}
