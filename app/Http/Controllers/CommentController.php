<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        Gate::authorize('create', Comment::class);

        $data = $request->validate([
            'body' => ['required', 'string', 'min:1'],
        ]);

        $post->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return back()->with('status', 'Comment added.');
    }

    public function edit(Comment $comment)
    {
        Gate::authorize('update', $comment);

        return view('comments.edit', compact('comment'));
    }

    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('update', $comment);

        $data = $request->validate([
            'body' => ['required', 'string', 'min:1'],
        ]);

        $comment->update($data);

        return redirect()->route('posts.show', $comment->post)->with('status', 'Comment updated.');
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return back()->with('status', 'Comment deleted.');
    }

    public function flag(Comment $comment)
    {
        $comment->update(['flagged' => true]);

        return back()->with('status', 'Comment flagged.');
    }

    public function unflag(Comment $comment)
    {
        Gate::authorize('moderate', $comment);

        $comment->update(['flagged' => false]);

        return back()->with('status', 'Comment unflagged.');
    }
}
