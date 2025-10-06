<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostFourSeeder extends Seeder
{
    public function run(): void
    {
        Post::truncate();
        User::truncate();

        $users = User::factory(10_000)->createQuietly();

        for ($j = 0; $j < 20; $j++) {
            $posts = Post::factory(5_000)->recycle($users)->make()->toArray();
            Post::insert($posts);
        }

    }
}
