<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;

class PostTwo extends Command
{
    protected $signature = 'app:post-two';

    protected $description = 'Post two data seeder';

    public function handle()
    {
        Post::truncate();
        User::truncate();

        $users = User::factory()->count(500)->create();

        $users->each(fn($user) =>
            Post::insert(
                Post::factory()->count(100)->make([
                    'user_id' => $user->id
                ])->toArray()
            )
        );
    }
}
