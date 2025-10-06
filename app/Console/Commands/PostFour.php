<?php

namespace App\Console\Commands;

use App\Models\Post;
use Benchmark;
use Database\Seeders\PostFourSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\table;

class PostFour extends Command
{
    protected $signature = 'app:post-four';

    protected $description = 'Post Four - Benchmark';

    public function handle()
    {
        $this->call(PostFourSeeder::class);

        $eloquentClosure = static fn () => Post::select(['id', 'title', 'user_id', 'published'])
            ->with(['user:id,name'])
            ->where('published', true)
            ->limit(5000)
            ->get();

        $queryBuilderClosure = static fn () => DB::table('posts')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->where('posts.published', true)
            ->limit(5000)
            ->select(['posts.id', 'posts.title', 'posts.user_id', 'posts.published', 'users.id', 'users.name'])
            ->get();

        $times = Benchmark::measure([
            'eloquentClosure' => $eloquentClosure,
            'queryBuilderClosure' => $queryBuilderClosure,
        ], 3);


        $measureMemory = static function (callable $function) {
            gc_collect_cycles();
            $start = memory_get_usage();
            $rows = $function();
            $end = memory_get_usage();
            return [
                'rows' => $rows->count(),
                'peak' => round(($end - $start) / 1024 / 1024, 2),
            ];
        };

        $eloquentMemory = $measureMemory($eloquentClosure);
        $queryBuilderMemory = $measureMemory($queryBuilderClosure);

        table(
            headers: ['Stat', 'Eloquent', 'Query Builder'],
            rows: [
                ['Rows', $eloquentMemory['rows'], $queryBuilderMemory['rows']],
                ['Time taken', $times['eloquentClosure'] . ' ms', $times['queryBuilderClosure'] . ' ms'],
                ['Memory Usage', $eloquentMemory['peak'] . ' MB', $queryBuilderMemory['peak'] . ' MB']
            ],
        );
    }
}
