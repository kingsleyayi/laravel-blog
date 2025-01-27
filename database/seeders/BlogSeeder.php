<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Create 1000 authors
        Author::factory(1000)
            ->create()
            ->each(function ($author) {
                // Each author gets 100-500 posts
                $posts = Post::factory(fake()->numberBetween(100, 500))
                    ->create(['author_id' => $author->id]);

                // Each post gets 1-50 comments
                $posts->each(function ($post) {
                    Comment::factory(fake()->numberBetween(1, 50))
                        ->create(['post_id' => $post->id]);
                });
            });
    }
}