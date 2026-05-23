<?php

namespace App\Http\Controllers;

use App\Models\Post;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->with('author:id,name')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9);

        return view('marketing.blog.index', ['posts' => $posts]);
    }

    public function show(Post $post)
    {
        abort_unless($post->is_published && (! $post->published_at || $post->published_at <= now()), 404);
        $post->load('author:id,name');

        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('marketing.blog.show', [
            'post'    => $post,
            'related' => $related,
        ]);
    }
}
