@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">Edit Comment</h1>
    <form method="POST" action="{{ route('comments.update', $comment) }}" class="space-y-4">
        @csrf
        @method('PATCH')
        <div>
            <textarea name="body" rows="6" class="w-full border rounded px-3 py-2" required>{{ old('body', $comment->body) }}</textarea>
            @error('body') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2">
            <a href="{{ route('posts.show', $comment->post) }}" class="px-4 py-2 border rounded">Cancel</a>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
        </div>
    </form>
</div>
@endsection
