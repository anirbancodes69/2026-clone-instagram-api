<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of posts.
     */
    public function index(Request $request): JsonResponse
    {
        $posts = Post::with('user')
            ->latest()
            ->paginate(15);

        $postsData = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'caption' => $post->caption,
                'image_url' => $post->image_url,
                'created_at' => $post->created_at,
                'user' => [
                    'id' => $post->user->id,
                    'username' => $post->user->username,
                    'avatar_url' => $post->user->avatar_url,
                ],
            ];
        });

        return $this->success([
            'posts' => $postsData,
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ], 'Posts retrieved successfully');
    }
}

