<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $posts
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content_raw' => 'required|string',
            'content_html' => 'required|string',
            'category_id' => 'required|exists:blog_categories,id',
            // ✅ Видалено user_id з валідації
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug'
        ]);

        // ✅ Автоматично встановлюємо user_id = 2
        $validated['user_id'] = 2;

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (BlogPost::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        if ($validated['is_published'] && !$validated['published_at']) {
            $validated['published_at'] = now();
        }

        $post = BlogPost::create($validated);
        $post->load(['user', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Пост успішно створено',
            'data' => $post
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::find($id);
        
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Пост не знайдено'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'excerpt' => 'sometimes|nullable|string|max:500',
            'content_raw' => 'sometimes|required|string',
            'content_html' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:blog_categories,id',
            // ✅ Видалено user_id з валідації - не можна змінювати
            'is_published' => 'sometimes|boolean',
            'published_at' => 'sometimes|nullable|date',
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('blog_posts', 'slug')->ignore($post->id)
            ]
        ]);

        if (isset($validated['title']) && !isset($validated['slug'])) {
            $newSlug = Str::slug($validated['title']);
            if ($newSlug !== $post->slug) {
                $originalSlug = $newSlug;
                $counter = 1;
                while (BlogPost::where('slug', $newSlug)->where('id', '!=', $post->id)->exists()) {
                    $newSlug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $validated['slug'] = $newSlug;
            }
        }

        if (isset($validated['is_published']) && $validated['is_published'] && !$post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);
        $post->load(['user', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Пост успішно оновлено',
            'data' => $post
        ]);
    }

    public function destroy($id)
    {
        $post = BlogPost::find($id);
        
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Пост не знайдено'
            ], 404);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Пост успішно видалено'
        ]);
    }
}