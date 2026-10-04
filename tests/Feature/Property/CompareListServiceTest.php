<?php

namespace Tests\Feature\Property;

use App\Models\Property;
use App\Services\Property\CompareListService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompareListServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_starts_empty(): void
    {
        $service = app(CompareListService::class);

        $this->assertSame([], $service->ids());
        $this->assertSame(0, $service->count());
        $this->assertFalse($service->isFull());
    }

    public function test_add_appends_and_persists_in_session(): void
    {
        $service = app(CompareListService::class);

        $this->assertTrue($service->add(1));
        $this->assertTrue($service->add(2));

        $this->assertSame([1, 2], $service->ids());
        $this->assertTrue($service->has(1));
        $this->assertFalse($service->has(3));
    }

    public function test_add_does_not_duplicate(): void
    {
        $service = app(CompareListService::class);

        $service->add(1);
        $added = $service->add(1);

        $this->assertFalse($added);
        $this->assertSame([1], $service->ids());
    }

    public function test_add_enforces_max_of_three(): void
    {
        $service = app(CompareListService::class);

        $service->add(1);
        $service->add(2);
        $service->add(3);

        $this->assertTrue($service->isFull());

        $added = $service->add(4);

        $this->assertFalse($added);
        $this->assertSame([1, 2, 3], $service->ids());
    }

    public function test_remove_drops_an_id(): void
    {
        $service = app(CompareListService::class);

        $service->add(1);
        $service->add(2);
        $service->remove(1);

        $this->assertSame([2], $service->ids());
    }

    public function test_clear_empties_the_list(): void
    {
        $service = app(CompareListService::class);

        $service->add(1);
        $service->clear();

        $this->assertSame([], $service->ids());
    }

    public function test_set_replaces_the_list(): void
    {
        $service = app(CompareListService::class);

        $service->add(1);
        $service->set([5, 6]);

        $this->assertSame([5, 6], $service->ids());
    }

    public function test_published_properties_excludes_unpublished_and_prunes_the_session(): void
    {
        $service = app(CompareListService::class);
        $published = Property::factory()->create();
        $draft = Property::factory()->draft()->create();

        $service->add($published->id);
        $service->add($draft->id);

        $properties = $service->publishedProperties();

        $this->assertSame([$published->id], $properties->pluck('id')->all());
        $this->assertSame([$published->id], $service->ids());
    }

    public function test_published_properties_returns_empty_collection_when_list_is_empty(): void
    {
        $service = app(CompareListService::class);

        $this->assertTrue($service->publishedProperties()->isEmpty());
    }
}
