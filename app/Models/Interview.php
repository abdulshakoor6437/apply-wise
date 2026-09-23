<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    use HasUuids;

    protected $fillable = [
        'application_id',
        'round_type',
        'scheduled_at',
        'format',
        'prep_notes',
        'outcome',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Accessor: get the company through the application relationship.
     */
    public function getCompanyAttribute(): ?Company
    {
        return $this->application?->company;
    }
}
