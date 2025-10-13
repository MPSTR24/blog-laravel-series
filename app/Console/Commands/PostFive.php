<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PostFive extends Command
{
    protected $signature = 'app:post-five';

    public function handle()
    {
        $userId = 1;

        // Query 1: Before index
        $queryOne = fn () => Post::where('user_id', $userId)
            ->where('published', true)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get(['id', 'title', 'user_id', 'published', 'created_at']);

        // Query 2: Before index
        $queryTwo = fn () => DB::table('posts')
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->where('posts.published', true)
            ->orderByDesc('posts.created_at')
            ->limit(20)
            ->get(['posts.id', 'posts.title', 'users.name', 'posts.created_at']);

        $baseline = Benchmark::measure([
            'one' => $queryOne,
            'two' => $queryTwo,
        ], 3);

        $this->table(['Case','Avg (ms)'], [
            ['one (no index)', $baseline['one']],
            ['two (no index)', $baseline['two']],
        ]);

        Schema::table('posts', function (Blueprint $table) {
            $table->index(['user_id', 'published', 'created_at'], 'posts_user_published_created_idx');
        });

        $after = Benchmark::measure([
            'one' => $queryOne,
            'two' => $queryTwo,
        ], 3);

        $this->table(['Case','Avg (ms)'], [
            ['one (with index)', $after['one']],
            ['two (with index)', $after['two']],
        ]);
    }
}
