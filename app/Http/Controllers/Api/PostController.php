<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostIndexRequest;
use App\Http\Resources\Api\PostResource;
use App\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/posts",
     *     summary="Get paginated posts with authors and comments",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="author_id",
     *         in="query",
     *         description="Filter posts by author ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Search posts by title",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object")
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(PostIndexRequest $request): JsonResource
    {
        $query = Post::query()
            ->with(['author', 'comments'])
            ->when($request->author_id, function ($query, $authorId) {
                return $query->where('author_id', $authorId);
            })
            ->when($request->title, function ($query, $title) {
                return $query->where('title', 'like', "%{$title}%");
            });

        $posts = $query->paginate(
            $request->per_page ?? 15
        );

        return PostResource::collection($posts);
    }
}