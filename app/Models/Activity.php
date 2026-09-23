<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasUuids;

    // Append-only — no updated_at column
    const UPDATED_AT = null;

    protected $fillable = [
        'application_id',
        'from_status',
        'to_status',
        'notes',
    ];

    /**
     * Default ordering: most recent first.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('latest', function ($query) {
            $query->orderByDesc('created_at');
        });
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
