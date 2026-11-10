<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ModerationLog extends Model
{
    protected $fillable = ['moderator_id', 'action', 'note'];

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function moderatable(): MorphTo
    {
        return $this->morphTo();
    }
}
