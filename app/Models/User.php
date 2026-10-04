<?php

namespace App\Models;

use App\Enums\Subscription\AgentSubscriptionStatus;
use App\Enums\User\UserRole;
use App\Enums\User\UserStatus;
use App\Observers\UserObserver;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property UserRole $role
 * @property string|null $google_id
 * @property string|null $avatar_url
 * @property UserStatus $status
 * @property Carbon|null $suspended_at
 * @property string|null $suspension_reason
 * @property Carbon|null $last_login_at
 * @property string|null $profile_photo_path
 * @property-read AgentSubscription|null $activeAgentSubscription
 * @property-read AgentProfile|null $agentProfile
 * @property-read Collection<int, AgentSubscription> $agentSubscriptions
 * @property-read int|null $agent_subscriptions_count
 * @property-read Collection<int, Favorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read mixed $profile_photo_url
 * @property-read Collection<int, Property> $properties
 * @property-read int|null $properties_count
 * @property-read Collection<int, SavedSearch> $savedSearches
 * @property-read int|null $saved_searches_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 */
#[Fillable(['name', 'email', 'password', 'role', 'google_id', 'avatar_url', 'status', 'suspended_at', 'suspension_reason'])]
#[Hidden(['password', 'remember_token'])]
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mirror the column defaults so a freshly created user has them in memory.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'active',
        'profile_photo_path' => null,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'suspended_at' => 'datetime',
        ];
    }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->profile_photo_path
                ? Storage::disk('public')->url($this->profile_photo_path)
                : null,
        );
    }

    /** @return HasOne<AgentProfile, $this> */
    public function agentProfile(): HasOne
    {
        return $this->hasOne(AgentProfile::class);
    }

    /** @return HasMany<Favorite, $this> */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /** @return HasMany<SavedSearch, $this> */
    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    /** @return HasMany<Property, $this> */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'agent_id');
    }

    /** @return HasMany<AgentSubscription, $this> */
    public function agentSubscriptions(): HasMany
    {
        return $this->hasMany(AgentSubscription::class, 'agent_id');
    }

    /** @return HasOne<AgentSubscription, $this> */
    public function activeAgentSubscription(): HasOne
    {
        return $this->hasOne(AgentSubscription::class, 'agent_id')
            ->where('status', AgentSubscriptionStatus::Active)
            ->where('expires_at', '>', now())
            ->latestOfMany();
    }
}
