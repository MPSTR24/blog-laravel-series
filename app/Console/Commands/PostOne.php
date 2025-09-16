<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;

class PostOne extends Command
{
    protected $signature = 'app:post-one';

    protected $description = 'N+1 Query demo';

    public function handle()
    {
        Post::truncate();
        User::truncate();

        User::factory()
            ->count(10)
            ->hasPosts(5)
            ->create();


        for ($i = 0; $i < 3; $i++) {
            DB::enableQueryLog();
            $time1 = Benchmark::value(static function () {
                $users = User::all();
                foreach ($users as $user) {
                    $user->posts;
                }
            });

            $queries1 = count(DB::getQueryLog());
            DB::flushQueryLog();

            $time2 = Benchmark::value(static function () {
                User::with('posts')->get();
            });

            $queries2 = count(DB::getQueryLog());
            DB::flushQueryLog();

            $this->info("Case 1 (N+1):  $time1[1] ms, {$queries1} queries");
            $this->info("Case 2 (with): $time2[1] ms, {$queries2} queries");
        }
    }
}
