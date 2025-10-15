@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 space-y-6">
    <div class="p-6 border rounded">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold">{{ $post->title }}</h1>
                <div class="text-gray-600 text-sm">by {{ $post->user->name }} • {{ $post->created_at->toDayDateTimeString() }}</div>
            </div>
            <div class="space-x-2">
                @can('update', $post)
                    <a href="{{ route('posts.edit', $post) }}" class="px-3 py-1 border rounded">Edit</a>
                @endcan
                @can('delete', $post)
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1 border rounded text-red-600" onclick="return confirm('Delete this post?')">Delete</button>
                    </form>
                @endcan
            </div>
        </div>
        <div class="mt-4 prose max-w-none">
            {!! nl2br(e($post->content)) !!}
        </div>
        <div class="mt-4 flex items-center justify-between">
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
            </div>
        </div>
    </div>

    <div class="p-6 border rounded">
        <h2 class="text-xl font-semibold mb-4">Comments ({{ $post->comments->count() }})</h2>
        @auth
        <form method="POST" action="{{ route('comments.store', $post) }}" class="space-y-3 mb-6">
            @csrf
            <textarea name="body" rows="4" class="w-full border rounded px-3 py-2" placeholder="Write a comment..." required>{{ old('body') }}</textarea>
            @error('body') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            <div class="flex justify-end">
                <button class="px-4 py-2 bg-gray-800 text-white rounded">Comment</button>
            </div>
        </form>
        @endauth

        <div class="space-y-4">
            @forelse ($post->comments as $comment)
                <div class="p-3 border rounded">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-gray-600">{{ $comment->user->name }} • {{ $comment->created_at->diffForHumans() }}</div>
                            <div class="mt-1">{{ $comment->body }}</div>
                            @if($comment->flagged)
                                <div class="mt-2 text-xs px-2 py-1 bg-red-100 text-red-700 rounded inline-block">Flagged</div>
                            @endif
                        </div>
                        <div class="space-x-2">
                            @can('update', $comment)
                                <a href="{{ route('comments.edit', $comment) }}" class="text-sm underline">Edit</a>
                            @endcan
                            @can('delete', $comment)
                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-600" onclick="return confirm('Delete comment?')">Delete</button>
                                </form>
                            @endcan
                            @auth
                                @if(!$comment->flagged)
                                <form action="{{ route('comments.flag', $comment) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-sm text-yellow-700">Flag</button>
                                </form>
                                @else
                                    @can('moderate', $comment)
                                    <form action="{{ route('comments.unflag', $comment) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-sm text-green-700">Clear Flag</button>
                                    </form>
                                    @endcan
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-gray-600">No comments yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
