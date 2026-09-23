<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case WISHLIST     = 'wishlist';
    case APPLIED      = 'applied';
    case PHONE_SCREEN = 'phone_screen';
    case INTERVIEW    = 'interview';
    case OFFER        = 'offer';
    case ACCEPTED     = 'accepted';
    case REJECTED     = 'rejected';
    case WITHDRAWN    = 'withdrawn';

    /**
     * Human-readable label for each status.
     */
    public function label(): string
    {
        return match($this) {
            self::WISHLIST     => 'Wishlist',
            self::APPLIED      => 'Applied',
            self::PHONE_SCREEN => 'Phone Screen',
            self::INTERVIEW    => 'Interview',
            self::OFFER        => 'Offer',
            self::ACCEPTED     => 'Accepted',
            self::REJECTED     => 'Rejected',
            self::WITHDRAWN    => 'Withdrawn',
        };
    }

    /**
     * Tailwind color class for badge/pill styling.
     */
    public function color(): string
    {
        return match($this) {
            self::WISHLIST     => 'gray',
            self::APPLIED      => 'blue',
            self::PHONE_SCREEN => 'yellow',
            self::INTERVIEW    => 'purple',
            self::OFFER        => 'green',
            self::ACCEPTED     => 'emerald',
            self::REJECTED     => 'red',
            self::WITHDRAWN    => 'slate',
        };
    }

    /**
     * Returns only the stages shown as Kanban columns.
     * Excludes ACCEPTED and WITHDRAWN (final/archive states).
     */
    public static function kanbanStages(): array
    {
        return [
            self::WISHLIST,
            self::APPLIED,
            self::PHONE_SCREEN,
            self::INTERVIEW,
            self::OFFER,
            self::REJECTED,
        ];
    }
}
