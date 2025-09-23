<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;

class PostTwoBenchmark extends Command
{
    protected $signature = 'benchmark:post-two';

    protected $description = 'Command to benchmark the post two route.';

    public function handle()
    {
        Benchmark::dd(static function () {

            $users = User::all();

            return $users->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'posts_count' => $user->posts->count(),
            ]);
        }, 3);
    }
}
