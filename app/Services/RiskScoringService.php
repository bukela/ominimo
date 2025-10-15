<?php

namespace App\Services;

use App\Models\Post;

class RiskScoringService
{
    /**
     * Calculate risk score and level for a post.
     */
    public function score(Post $post): array
    {
        $score = 0;

        $content = strtolower($post->title . ' ' . $post->content);

        $keywords = ['accident', 'fire', 'theft', 'damage'];
        foreach ($keywords as $kw) {
            if (str_contains($content, $kw)) {
                $score += 50;
                break;
            }
        }

        if (mb_strlen($post->content) < 50) {
            $score += 10;
        }

        if ($score === 0) {
            $score += 20;
        }

        $level = 'low';
        if ($score >= 70) {
            $level = 'high';
        } elseif ($score >= 30) {
            $level = 'medium';
        }

        return ['score' => $score, 'level' => $level];
    }
}
