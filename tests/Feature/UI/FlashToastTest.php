<?php

namespace Tests\Feature\UI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashToastTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_flash_renders_as_a_styled_toast(): void
    {
        $response = $this->withSession(['success' => 'Test success message'])->get('/login');

        $response->assertOk();
        $response->assertSee('Test success message');
        $response->assertSee('data-toast-type="success"', false);
    }

    public function test_error_flash_renders_as_a_styled_toast(): void
    {
        $response = $this->withSession(['error' => 'Test error message'])->get('/login');

        $response->assertOk();
        $response->assertSee('Test error message');
        $response->assertSee('data-toast-type="error"', false);
    }

    public function test_status_flash_renders_as_a_success_styled_toast(): void
    {
        $response = $this->withSession(['status' => 'We have emailed your password reset link.'])->get('/login');

        $response->assertOk();
        $response->assertSee('We have emailed your password reset link.');
        $response->assertSee('data-toast-type="success"', false);
    }

    public function test_no_flash_renders_no_toast(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertDontSee('data-toast-type', false);
    }
}
