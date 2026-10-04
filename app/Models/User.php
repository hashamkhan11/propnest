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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'google_id', 'avatar_url', 'status', 'suspended_at', 'suspension_reason'])]
#[Hidden(['password', 'remember_token'])]
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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

    public function agentProfile(): HasOne
    {
        return $this->hasOne(AgentProfile::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'agent_id');
    }

    public function agentSubscriptions(): HasMany
    {
        return $this->hasMany(AgentSubscription::class, 'agent_id');
    }

    public function activeAgentSubscription(): HasOne
    {
        return $this->hasOne(AgentSubscription::class, 'agent_id')
            ->where('status', AgentSubscriptionStatus::Active)
            ->where('expires_at', '>', now())
            ->latestOfMany();
    }
}
