<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public static function roleProvider(): array
    {
        return [
            'buyer' => [null],
            'agent' => ['agent'],
            'admin' => ['admin'],
            'super admin' => ['superAdmin'],
        ];
    }

    #[DataProvider('roleProvider')]
    public function test_user_can_upload_a_profile_photo(?string $factoryState): void
    {
        Storage::fake('public');

        $user = $factoryState ? User::factory()->{$factoryState}()->create() : User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.update-profile-photo-form')
            ->set('photo', UploadedFile::fake()->image('avatar.jpg'));

        $component->assertHasNoErrors();

        $user->refresh();

        $this->assertNotNull($user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);
        $this->assertNotNull($user->profile_photo_url);
    }

    public function test_uploading_a_new_photo_replaces_and_deletes_the_old_one(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        Volt::test('profile.update-profile-photo-form')
            ->set('photo', UploadedFile::fake()->image('first.jpg'));

        $firstPath = $user->refresh()->profile_photo_path;

        Volt::test('profile.update-profile-photo-form')
            ->set('photo', UploadedFile::fake()->image('second.jpg'));

        $user->refresh();

        $this->assertNotSame($firstPath, $user->profile_photo_path);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($user->profile_photo_path);
    }

    public function test_user_can_remove_their_profile_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        Volt::test('profile.update-profile-photo-form')
            ->set('photo', UploadedFile::fake()->image('avatar.jpg'));

        $path = $user->refresh()->profile_photo_path;

        Volt::test('profile.update-profile-photo-form')
            ->call('removePhoto');

        $user->refresh();

        $this->assertNull($user->profile_photo_path);
        $this->assertNull($user->profile_photo_url);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_photo_must_be_an_allowed_image_type(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        // .svg passes Laravel's generic "image" rule but is excluded by the
        // explicit mimes whitelist, so this targets that stricter check.
        Volt::test('profile.update-profile-photo-form')
            ->set('photo', UploadedFile::fake()->create('document.svg', 100))
            ->assertHasErrors(['photo' => 'mimes']);

        $this->assertNull($user->refresh()->profile_photo_path);
    }

    public function test_photo_must_not_exceed_the_size_limit(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        Volt::test('profile.update-profile-photo-form')
            ->set('photo', UploadedFile::fake()->image('big.jpg')->size(3000))
            ->assertHasErrors(['photo' => 'max']);

        $this->assertNull($user->refresh()->profile_photo_path);
    }
}
