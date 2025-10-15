<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\RiskScoringService;
use App\Jobs\ProcessPostRisk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'tag', 'sort', 'direction']);

        $posts = Post::query()
            ->with(['user', 'tags'])
            ->withCount('comments')
            ->withFilters($filters)
            ->paginate(10)
            ->withQueryString();

        return view('posts.index', compact('posts', 'filters'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'tags' => ['nullable', 'string'],
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        $post->syncTagsFromString($data['tags'] ?? null);

        // Queue risk scoring
        ProcessPostRisk::dispatch($post->id);

        return redirect()->route('posts.show', $post)->with('status', 'Post created.');
    }

    public function show(Post $post)
    {
        $post->load(['user', 'tags', 'comments.user']);

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        $tags = $post->tags->pluck('name')->implode(', ');

        return view('posts.edit', compact('post', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        Gate::authorize('update', $post);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'tags' => ['nullable', 'string'],
        ]);

        $post->update([
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        $post->syncTagsFromString($data['tags'] ?? null);

        // Re-score risk
        ProcessPostRisk::dispatch($post->id);

        return redirect()->route('posts.show', $post)->with('status', 'Post updated.');
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')->with('status', 'Post deleted.');
    }
}
