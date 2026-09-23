<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'company_id',
        'job_title',
        'status',
        'date_applied',
        'source',
        'job_posting_url',
        'salary_min',
        'salary_max',
        'excitement_rating',
        'priority',
        'next_action_date',
        'notes',
    ];

    protected $casts = [
        'status'           => ApplicationStatus::class,
        'date_applied'     => 'date',
        'next_action_date' => 'date',
        'salary_min'       => 'decimal:2',
        'salary_max'       => 'decimal:2',
    ];

    public $originalStatusForActivity;

    /**
     * Boot method — automatically logs an Activity whenever status is set or changes.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function (Application $application) {
            Activity::create([
                'application_id' => $application->id,
                'from_status'    => null,
                'to_status'      => $application->status instanceof ApplicationStatus
                    ? $application->status->value
                    : $application->status,
            ]);
        });

        static::updating(function (Application $application) {
            if ($application->isDirty('status')) {
                $application->originalStatusForActivity = $application->getOriginal('status');
            }
        });

        static::updated(function (Application $application) {
            if (isset($application->originalStatusForActivity)) {
                $original = $application->originalStatusForActivity;
                unset($application->originalStatusForActivity);

                Activity::create([
                    'application_id' => $application->id,
                    'from_status'    => $original instanceof ApplicationStatus
                        ? $original->value
                        : $original,
                    'to_status'      => $application->status instanceof ApplicationStatus
                        ? $application->status->value
                        : $application->status,
                ]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'application_document')
                    ->withTimestamps();
    }

    /**
     * Format salary range using K notation (e.g., "$150k — $200k")
     */
    public function getFormattedSalaryAttribute(): ?string
    {
        if (is_null($this->salary_min) && is_null($this->salary_max)) {
            return null;
        }

        $formatK = fn($val) => '$' . (round($val / 1000) . 'k');

        if ($this->salary_min && $this->salary_max) {
            if ($this->salary_min == $this->salary_max) {
                return $formatK($this->salary_min);
            }
            return $formatK($this->salary_min) . ' — ' . $formatK($this->salary_max);
        }

        return $this->salary_min ? $formatK($this->salary_min) : $formatK($this->salary_max);
    }

    /**
     * Format date applied into human readable relative format
     */
    public function getFormattedDateAppliedAttribute(): ?string
    {
        if (is_null($this->date_applied)) {
            return null;
        }

        $date = \Illuminate\Support\Carbon::parse($this->date_applied);
        if ($date->isToday()) return 'Today';
        if ($date->isYesterday()) return 'Yesterday';
        if ($date->gt(now()->subDays(7))) return $date->diffForHumans();

        return $date->format('M j, Y');
    }
}
