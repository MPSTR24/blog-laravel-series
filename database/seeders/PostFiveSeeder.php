<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostFiveSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(10_000)->createQuietly();

        for ($j = 0; $j < 20; $j++) {
            $posts = Post::factory(5_000)->recycle($users)->make()->toArray();
            Post::insert($posts);
        }
    }
}
