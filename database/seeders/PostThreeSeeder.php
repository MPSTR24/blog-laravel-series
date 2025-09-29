<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostThreeSeeder extends Seeder
{
    public function run(): void
    {
        Comment::truncate();
        Post::truncate();
        User::truncate();

        $chunkSize = 1_000;
        $totalPosts = 5_000;

        $user = User::factory()->create();

        for ($i = 0; $i < $totalPosts; $i += $chunkSize) {
            $posts = Post::factory()
                ->recycle($user)
                ->count($chunkSize)
                ->make([
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            Post::insert($posts->toArray());
        }

        Post::chunk($chunkSize, static function ($posts) {
            $allComments = [];
            foreach ($posts as $post) {
                $allComments[] = Comment::factory()
                    ->count(random_int(10, 20))
                    ->make([
                        'post_id'    => $post->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                    ->toArray();
            }

            $allComments = array_merge(...$allComments);
            foreach (array_chunk($allComments, 5000) as $chunk) {
                Comment::insert($chunk);
            }
        });
    }
}
