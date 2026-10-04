<?php

namespace App\Services\Property;

use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use Illuminate\Support\Collection;

class CompareListService
{
    private const SESSION_KEY = 'compare_properties';

    private const MAX_ITEMS = 3;

    public function ids(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function has(int $propertyId): bool
    {
        return in_array($propertyId, $this->ids(), true);
    }

    public function count(): int
    {
        return count($this->ids());
    }

    public function isFull(): bool
    {
        return $this->count() >= self::MAX_ITEMS;
    }

    public function add(int $propertyId): bool
    {
        $ids = $this->ids();

        if (in_array($propertyId, $ids, true) || count($ids) >= self::MAX_ITEMS) {
            return false;
        }

        $ids[] = $propertyId;
        session([self::SESSION_KEY => $ids]);

        return true;
    }

    public function remove(int $propertyId): void
    {
        $ids = array_values(array_filter(
            $this->ids(),
            fn (int $id) => $id !== $propertyId
        ));

        session([self::SESSION_KEY => $ids]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function set(array $ids): void
    {
        session([self::SESSION_KEY => array_values($ids)]);
    }

    /**
     * The single source of truth for "which properties are actually in the
     * compare list right now": loads only published properties, preserves
     * session order, and prunes any stale/unpublished ids from the session.
     *
     * @param  array<int, string>  $with  Relations to eager-load.
     */
    public function publishedProperties(array $with = []): Collection
    {
        $ids = $this->ids();

        if (empty($ids)) {
            return collect();
        }

        $properties = Property::with($with)
            ->withCount('inquiries')
            ->whereIn('id', $ids)
            ->where('status', PropertyStatus::Published)
            ->get()
            ->keyBy('id');

        $validIds = array_values(array_filter($ids, fn (int $id) => $properties->has($id)));

        if ($validIds !== array_values($ids)) {
            $this->set($validIds);
        }

        return collect($validIds)->map(fn (int $id) => $properties->get($id));
    }
}
