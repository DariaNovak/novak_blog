<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with(['user', 'category'])->get();
        
        return response()->json([
            'data' => $posts,
            'status' => 'success'
        ]);
    }

    public function show($id)
    {
        $post = BlogPost::with(['user', 'category'])->find($id);
        
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Пост не знайдено'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $post
        ]);
    }
}