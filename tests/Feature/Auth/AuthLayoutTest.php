<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class AuthLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_url_renders_as_a_modal_over_the_homepage(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Find a place');
        $response->assertSee('data-initial-open="true"', false);
    }

    public function test_role_selector_component_renders_buyer_and_agent_options(): void
    {
        $html = Blade::render('<x-role-selector name="role" :value="$value" />', ['value' => 'buyer']);

        $this->assertStringContainsString('value="buyer"', $html);
        $this->assertStringContainsString('value="agent"', $html);
        $this->assertStringContainsString('Find a home', $html);
        $this->assertStringContainsString('List homes', $html);
        $this->assertStringContainsString('wire:model.live="role"', $html);
    }

    public function test_role_selector_cards_use_the_compact_sizing(): void
    {
        $html = Blade::render('<x-role-selector name="role" :value="$value" />', ['value' => 'buyer']);

        $this->assertStringContainsString('w-4 h-4', $html);
        $this->assertStringContainsString('py-3', $html);
    }
}
