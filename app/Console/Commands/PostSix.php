<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;

class PostSix extends Command
{
    protected $signature = 'app:post-six';

    public function handle()
    {
        DB::connection()->disableQueryLog();

        $user = User::firstOrCreate();

        $rows = Post::factory(100_000)->make(['user_id' => $user->id])->toArray();

        $chunkSize = 1_000;

        $queryOne = function () use ($rows, $chunkSize) {
            DB::transaction(function () use ($rows, $chunkSize) {
                foreach (array_chunk($rows, $chunkSize) as $chunk) {
                    DB::table('posts')->insert($chunk);
                }
            });
        };

        $queryTwo = function () use ($rows) {
            foreach ($rows as $data) {
                Post::create($data);
            }
        };

        $benchmarks = Benchmark::measure([
            'queryOne' => $queryOne,
            'queryTwo' => $queryTwo,
        ], 3);


        $this->table(['Case','Avg (ms)'], [
            ['Batching', $benchmarks['queryOne']],
            ['For Loop', $benchmarks['queryTwo']],
        ]);
    }
}
