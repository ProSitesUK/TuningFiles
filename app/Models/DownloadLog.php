<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadLog extends Model
{
    protected $guarded = [];

    public function orderFile(): BelongsTo { return $this->belongsTo(OrderFile::class); }
    public function user(): BelongsTo      { return $this->belongsTo(User::class); }
}
