<?php

use App\Models\Author;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('it can list paginated posts with authors and comments', function () {
    $author = Author::factory()->create();
    Post::factory(20)
        ->for($author)
        ->has(Comment::factory(3))
        ->create();

    $response = $this->get('/api/posts');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'title',
                    'content',
                    'published_at',
                    'author' => ['name'],
                    'comments' => [
                        '*' => [
                            'name',
                            'text',
                        ],
                    ],
                ],
            ],
            'links',
            'meta',
        ])
        ->assertJsonCount(15, 'data');
});

test('it can change items per page', function () {
    $author = Author::factory()->create();
    Post::factory(30)
        ->for($author)
        ->has(Comment::factory(3))
        ->create();

    $response = $this->get('/api/posts?per_page=20');

    $response->assertStatus(200)
        ->assertJsonCount(20, 'data')
        ->assertJsonStructure([
            'meta' => [
                'current_page',
                'per_page',
                'total',
            ],
        ]);
});

test('it can navigate to different pages', function () {
    $author = Author::factory()->create();
    Post::factory(30)
        ->for($author)
        ->has(Comment::factory(3))
        ->create();

    $response = $this->get('/api/posts?page=2&per_page=10');

    $response->assertStatus(200)
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonCount(10, 'data');
});

test('it can filter posts by author_id', function () {
    $author = Author::factory()->create();
    Post::factory(5)->for($author)->create();

    $response = $this->get("/api/posts?author_id={$author->id}");

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data');
});

test('it can filter posts by title', function () {
    $author = Author::factory()->create();
    $post = Post::factory()
        ->for($author)
        ->create(['title' => 'Unique Title For Testing']);

    $response = $this->get('/api/posts?title=Unique');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Unique Title For Testing');
});

test('it validates author_id must be integer', function () {
    $response = $this->get('/api/posts?author_id=invalid');

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['author_id']);
});

test('it validates author must exist', function () {
    $response = $this->get('/api/posts?author_id=99999');

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['author_id']);
});

test('it validates per_page cannot exceed 100', function () {
    $response = $this->get('/api/posts?per_page=101');

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['per_page']);
});

test('it optimizes queries using eager loading', function () {
    $author = Author::factory()->create();
    Post::factory(2)
        ->for($author)
        ->has(Comment::factory(3))
        ->create();

    DB::flushQueryLog();
    DB::enableQueryLog();

    $this->get('/api/posts');

    $queries = DB::getQueryLog();

    // Filter only SELECT queries
    $selectQueries = collect($queries)->filter(fn($q) => str_starts_with($q['query'], 'select'));

    $postSelects = $selectQueries->filter(fn($q) => str_contains($q['query'], 'posts'))->count();
    $authorSelects = $selectQueries->filter(fn($q) => str_contains($q['query'], 'authors'))->count();
    $commentSelects = $selectQueries->filter(fn($q) => str_contains($q['query'], 'comments'))->count();

    expect($postSelects)->toBe(2) // 1 for data, 1 for pagination count
        ->and($authorSelects)->toBe(1)
        ->and($commentSelects)->toBe(1);
});
