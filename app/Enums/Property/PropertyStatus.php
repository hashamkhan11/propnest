<?php

namespace App\Enums\Property;

enum PropertyStatus: string
{
    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case Published = 'published';
    case Rejected = 'rejected';
    case Archived = 'archived';
    case Sold = 'sold';
    case Rented = 'rented';
    case UnderOffer = 'under_offer';

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::Draft => $to === self::PendingReview,
            self::Published => in_array($to, [self::UnderOffer, self::Sold, self::Rented, self::Archived], true),
            self::UnderOffer => in_array($to, [self::Published, self::Sold, self::Rented, self::Archived], true),
            self::Archived, self::Sold, self::Rented, self::Rejected => $to === self::Draft,
            self::PendingReview => false,
        };
    }

    /**
     * @return array<int, self>
     */
    public function validTransitions(): array
    {
        return array_values(array_filter(self::cases(), fn (self $case) => $this->canTransitionTo($case)));
    }

    public function label(): string
    {
        return \Illuminate\Support\Str::headline($this->value);
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Published => 'success',
            self::Archived, self::Rejected => 'danger',
            self::Draft => 'gray',
            default => 'warning',
        };
    }
}
