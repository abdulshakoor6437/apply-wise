<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Document extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'label',
        'doc_type',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'application_document')
                    ->withTimestamps();
    }

    /**
     * Computed accessor: percentage of linked applications that reached
     * at least phone_screen status (phone_screen, interview, offer, accepted).
     * Returns null if no applications are linked.
     */
    public function getResponseRateAttribute(): ?float
    {
        $apps = $this->applications;

        if ($apps->isEmpty()) {
            return null;
        }

        $advancedStatuses = [
            ApplicationStatus::PHONE_SCREEN->value,
            ApplicationStatus::INTERVIEW->value,
            ApplicationStatus::OFFER->value,
            ApplicationStatus::ACCEPTED->value,
        ];

        $respondedCount = $apps->filter(function (Application $app) use ($advancedStatuses) {
            $statusValue = $app->status instanceof ApplicationStatus
                ? $app->status->value
                : $app->status;

            return in_array($statusValue, $advancedStatuses, true);
        })->count();

        return round(($respondedCount / $apps->count()) * 100, 1);
    }

    /**
     * Computed accessor: human readable file size ("245 KB", "2.4 MB")
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size ?: 0;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024) . ' KB';
        }
        return $bytes . ' B';
    }
}
