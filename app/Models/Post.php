<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'is_published' => 'bool',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)
                 ->where(function ($qq) {
                     $qq->whereNull('published_at')->orWhere('published_at', '<=', now());
                 });
    }

    public function bodyHtml(): string
    {
        return $this->body ? Str::markdown($this->body) : '';
    }

    public function readingMinutes(): int
    {
        $words = str_word_count(strip_tags((string) $this->body));
        return max(1, (int) round($words / 200));
    }
}
