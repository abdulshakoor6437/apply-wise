<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasUuids;

    protected $fillable = [
        'company_id',
        'name',
        'role',
        'email',
        'phone',
        'linkedin_url',
        'last_contacted_at',
        'notes',
    ];

    protected $casts = [
        'last_contacted_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
