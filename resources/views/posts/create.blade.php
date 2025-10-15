@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Create Post</h1>

    <form method="POST" action="{{ route('posts.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium">Title</label>
            <input name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
            @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Content</label>
            <textarea name="content" rows="8" class="w-full border rounded px-3 py-2" required>{{ old('content') }}</textarea>
            @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Tags (comma separated)</label>
            <input name="tags" value="{{ old('tags') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="flex gap-3">
            <a href="{{ route('posts.index') }}" class="px-4 py-2 border rounded">Cancel</a>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
        </div>
    </form>
</div>
@endsection
