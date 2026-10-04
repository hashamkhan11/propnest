<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['filters', 'alerts_enabled'])]
class SavedSearch extends Model
{
    protected $attributes = [
        'alerts_enabled' => true,
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'alerts_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function label(): string
    {
        $filters = $this->filters;
        $parts = [];

        if (($filters['propertyType'] ?? '') !== '') {
            $parts[] = ucfirst($filters['propertyType']);
        }

        $min = $filters['minPrice'] ?? '';
        $max = $filters['maxPrice'] ?? '';

        if ($min !== '' || $max !== '') {
            $parts[] = match (true) {
                $min !== '' && $max !== '' => '$'.number_format((float) $min).'–$'.number_format((float) $max),
                $min !== '' => '$'.number_format((float) $min).'+',
                default => 'Up to $'.number_format((float) $max),
            };
        }

        if (($filters['bedrooms'] ?? '') !== '') {
            $parts[] = $filters['bedrooms'].'+ bed';
        }

        if (($filters['location'] ?? '') !== '') {
            $parts[] = $filters['location'];
        }

        if (($filters['keyword'] ?? '') !== '') {
            $parts[] = '"'.$filters['keyword'].'"';
        }

        return $parts === [] ? 'All listings' : implode(' · ', $parts);
    }

    public function resultsUrl(): string
    {
        $query = array_filter(
            $this->filters,
            fn ($value) => $value !== '' && $value !== [] && $value !== null
        );

        return route('properties.index', $query);
    }
}
