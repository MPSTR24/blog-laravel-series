<?php

namespace App\Console\Commands;

use App\Models\Post;
use Database\Seeders\PostThreeSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Cache;

class PostThree extends Command
{
    /*
     * Chunking implemented to avoid too many variables in SQLite
     */

    protected $signature = 'app:post-three';

    protected $description = 'Post three - cache demo';

    public function handle()
    {
        $this->call(PostThreeSeeder::class);

        // Clear cache for clean slate
        Cache::forget('popular_posts');

        // Trigger cached query once so first call does not affect averages
        $this->cachedQuery();


        Benchmark::dd([
            'No Cache' => fn() => $this->query(),
            'Cache' => fn() => $this->cachedQuery(),
        ], 3);
    }

    public function query(){
        return Post::withCount('comments')
            ->orderByDesc('comments_count')
            ->limit(10)
            ->get();
    }

    public function cachedQuery(){
        return Cache::remember('popular_posts', 3600, static fn() =>
            Post::withCount('comments')
                ->orderByDesc('comments_count')
                ->limit(10)
                ->get()
        );
    }
}
