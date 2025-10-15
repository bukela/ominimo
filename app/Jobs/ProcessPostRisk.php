<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\User;
use App\Services\RiskScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPostRisk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $postId)
    {
    }

    public function handle(RiskScoringService $scorer): void
    {
        $post = Post::find($this->postId);
        if (!$post) {
            return;
        }

        $result = $scorer->score($post);

        $post->update([
            'risk_score' => $result['score'],
            'risk_level' => $result['level'],
        ]);

        if ($result['level'] === 'high') {
            // For brevity, log a message. Could be Mail::to($admins)->queue(...)
            $adminIds = User::where('role', User::ROLE_ADMIN)->pluck('id')->all();
            Log::warning('High risk post detected', [
                'post_id' => $post->id,
                'title' => $post->title,
                'admins_notified' => $adminIds,
            ]);
        }
    }
}
