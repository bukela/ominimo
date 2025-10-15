<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;

Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard stats page (HTML)
Route::get('/dashboard/stats', function () {
    $user = auth()->user();
    if (!$user || (! $user->isAdmin() && ! $user->isModerator())) {
        abort(403);
    }

    $totals = [
        'users' => User::count(),
        'posts' => Post::count(),
        'comments' => Comment::count(),
        'flagged_comments' => Comment::where('flagged', true)->count(),
    ];

    $topUsers = User::withCount(['posts', 'comments'])
        ->orderByDesc('posts_count')
        ->limit(5)
        ->get(['id', 'name', 'email', 'role', 'created_at']);

    return view('dashboard.stats', compact('totals', 'topUsers'));
})->middleware(['auth'])->name('dashboard.stats');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Posts CRUD: protected (create, store, edit, update, destroy)
Route::resource('posts', PostController::class)->except(['index', 'show'])->middleware(['auth']);

// Posts CRUD: public (index, show)
Route::resource('posts', PostController::class)->only(['index', 'show']);

// Comments CRUD (create/update/delete)
Route::post('posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('auth');
Route::get('comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit')->middleware('auth');
Route::patch('comments/{comment}', [CommentController::class, 'update'])->name('comments.update')->middleware('auth');
Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy')->middleware('auth');

// Comment flagging
Route::post('comments/{comment}/flag', [CommentController::class, 'flag'])->name('comments.flag')->middleware('auth');
Route::delete('comments/{comment}/flag', [CommentController::class, 'unflag'])->name('comments.unflag')->middleware('auth');

require __DIR__.'/auth.php';
