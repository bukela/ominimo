@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-8">
    <div class="bg-white border rounded-lg p-8">
        <h1 class="text-3xl font-semibold">Welcome to the Blog</h1>
        <p class="text-gray-600 mt-2">Explore posts, engage with comments, and view dashboard insights.</p>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('posts.index') }}" class="px-5 py-2.5 bg-blue-600 text-white rounded shadow hover:bg-blue-700">Browse Posts</a>

            @auth
                <a href="{{ route('posts.create') }}" class="px-5 py-2.5 bg-emerald-600 text-white rounded shadow hover:bg-emerald-700">Create Post</a>
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-gray-800 text-white rounded shadow hover:bg-black/80">Dashboard</a>
                <a href="{{ route('dashboard.stats') }}" class="px-5 py-2.5 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700">Stats</a>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2.5 border rounded hover:bg-gray-50">Log in</a>
                <a href="{{ route('register') }}" class="px-5 py-2.5 border rounded hover:bg-gray-50">Register</a>
            @endauth
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border rounded-lg p-6">
            <h2 class="text-lg font-semibold">Posts</h2>
            <p class="text-gray-600 mt-1">Create, edit, and manage your blog posts. Tag and filter posts easily.</p>
            <a href="{{ route('posts.index') }}" class="mt-3 inline-block text-blue-600 hover:underline">Go to Posts →</a>
        </div>

        <div class="bg-white border rounded-lg p-6">
            <h2 class="text-lg font-semibold">Discussion</h2>
            <p class="text-gray-600 mt-1">Join the conversation in post comments and share your thoughts.</p>
            <a href="{{ route('posts.index', ['sort' => 'created_at']) }}" class="mt-3 inline-block text-blue-600 hover:underline">Browse Posts →</a>
        </div>

        <div class="bg-white border rounded-lg p-6">
            <h2 class="text-lg font-semibold">Analytics</h2>
            <p class="text-gray-600 mt-1">View totals and top users in the dashboard.</p>
            @auth
                <a href="{{ route('dashboard.stats') }}" class="mt-3 inline-block text-blue-600 hover:underline">Open Stats →</a>
            @else
                <p class="mt-3 text-gray-600">Sign in to view stats.</p>
            @endauth
        </div>
    </div>
</div>
@endsection
