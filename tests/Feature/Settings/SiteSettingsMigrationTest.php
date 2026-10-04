<?php

namespace Tests\Feature\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SiteSettingsMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_seeds_exactly_one_default_row(): void
    {
        $rows = DB::table('site_settings')->get();

        $this->assertCount(1, $rows);
        $this->assertSame('USD', $rows->first()->currency);
    }
}
