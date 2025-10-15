<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'risk_score',
        'risk_level',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function scopeWithFilters($query, array $filters = [])
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($qq) use ($search) {
                $qq->where('title', 'like', "%{$search}%")
                   ->orWhere('content', 'like', "%{$search}%");
            });
        });

        $query->when($filters['tag'] ?? null, function ($q, $tag) {
            $q->whereHas('tags', fn($tq) => $tq->where('name', $tag));
        });

        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';
        if (in_array($sort, ['created_at', 'title', 'risk_score']) && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sort, $direction);
        }

        return $query;
    }

    public function syncTagsFromString(?string $tagsCsv): void
    {
        $names = collect(explode(',', (string) $tagsCsv))
            ->map(fn($t) => trim($t))
            ->filter()
            ->unique();

        if ($names->isEmpty()) {
            $this->tags()->sync([]);
            return;
        }

        $tagIds = Tag::whereIn('name', $names)->pluck('id', 'name')->all();

        $ids = [];
        foreach ($names as $name) {
            if (isset($tagIds[$name])) {
                $ids[] = $tagIds[$name];
            } else {
                $ids[] = Tag::create(['name' => $name])->id;
            }
        }

        $this->tags()->sync($ids);
    }
}
