<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;


class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence();
        
        return [
            'title' => $title,
            'content' => fake()->paragraphs(3, true),
            'slug' => Str::slug($title),
            'published_at' => Carbon::now()->subDays(rand(1, 365))->format('Y-m-d H:i:s'),
        ];
    }
}