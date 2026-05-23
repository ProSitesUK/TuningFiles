<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $table = 'seo_meta';
    protected $guarded = [];
    protected $casts = ['structured_data' => 'array'];

    public static function forSubject(string $type, string $key): ?self
    {
        return self::where('subject_type', $type)->where('subject_key', $key)->first();
    }
}
