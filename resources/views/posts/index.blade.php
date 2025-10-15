@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Posts</h1>
        @auth
            <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">New Post</a>
        @endauth
    </div>

    <form method="GET" action="{{ route('posts.index') }}" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="search" placeholder="Search..." value="{{ $filters['search'] ?? '' }}" class="border rounded px-3 py-2">
        <input type="text" name="tag" placeholder="Tag" value="{{ $filters['tag'] ?? '' }}" class="border rounded px-3 py-2">
        <select name="sort" class="border rounded px-3 py-2">
            <option value="created_at" @selected(($filters['sort'] ?? '') === 'created_at')>Created</option>
            <option value="title" @selected(($filters['sort'] ?? '') === 'title')>Title</option>
            <option value="risk_score" @selected(($filters['sort'] ?? '') === 'risk_score')>Risk</option>
        </select>
        <select name="direction" class="border rounded px-3 py-2">
            <option value="desc" @selected(($filters['direction'] ?? '') === 'desc')>Desc</option>
            <option value="asc" @selected(($filters['direction'] ?? '') === 'asc')>Asc</option>
        </select>
        <div class="md:col-span-4">
            <button class="px-4 py-2 bg-gray-800 text-white rounded">Apply</button>
        </div>
    </form>

    <div class="space-y-4">
        @forelse ($posts as $post)
            <div class="p-4 border rounded">
                <div class="flex justify-between items-center">
                    <a href="{{ route('posts.show', $post) }}" class="text-lg font-medium hover:underline">{{ $post->title }}</a>
                    <div class="text-sm text-gray-500">by {{ $post->user->name }} • {{ $post->created_at->diffForHumans() }}</div>
                </div>
                <div class="mt-2 text-gray-700 line-clamp-2">{{ \Illuminate\Support\Str::limit($post->content, 200) }}</div>
                <div class="mt-2 flex items-center justify-between">
                    <div class="text-sm">
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('posts.index', ['tag' => $tag->name]) }}" class="inline-block px-2 py-1 bg-gray-100 rounded mr-2">#{{ $tag->name }}</a>
                        @endforeach
                    </div>
                    <div class="text-sm">
                        @if($post->risk_level)
                            <span class="px-2 py-1 rounded {{ $post->risk_level === 'high' ? 'bg-red-100 text-red-700' : ($post->risk_level === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                Risk: {{ $post->risk_level }} ({{ $post->risk_score }})
                            </span>
                        @endif
                        <span class="ml-2 text-gray-500">{{ $post->comments_count }} comments</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-gray-600">No posts found.</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>
@endsection
