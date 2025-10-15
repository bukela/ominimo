@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-8">
    <div>
        <h1 class="text-2xl font-semibold">Dashboard Stats</h1>
        <p class="text-gray-600 mt-1">Overview of users, posts, comments, and flagged items.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="p-4 border rounded bg-white">
            <div class="text-sm text-gray-500">Users</div>
            <div class="text-2xl font-bold mt-1">{{ $totals['users'] }}</div>
        </div>
        <div class="p-4 border rounded bg-white">
            <div class="text-sm text-gray-500">Posts</div>
            <div class="text-2xl font-bold mt-1">{{ $totals['posts'] }}</div>
        </div>
        <div class="p-4 border rounded bg-white">
            <div class="text-sm text-gray-500">Comments</div>
            <div class="text-2xl font-bold mt-1">{{ $totals['comments'] }}</div>
        </div>
        <div class="p-4 border rounded bg-white">
            <div class="text-sm text-gray-500">Flagged Comments</div>
            <div class="text-2xl font-bold mt-1">{{ $totals['flagged_comments'] }}</div>
        </div>
    </div>

    <div class="p-4 border rounded bg-white">
        <h2 class="text-lg font-semibold mb-3">Top Users</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Role</th>
                        <th class="px-4 py-2">Posts</th>
                        <th class="px-4 py-2">Comments</th>
                        <th class="px-4 py-2">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topUsers as $u)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $u->name }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $u->email }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded bg-gray-100">{{ $u->role }}</span>
                            </td>
                            <td class="px-4 py-2">{{ $u->posts_count }}</td>
                            <td class="px-4 py-2">{{ $u->comments_count }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $u->created_at->toDateString() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-600">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
