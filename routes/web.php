<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\Public\Home::class)->name('home');

Route::get('dashboard', function () {
    return match (auth()->user()->role) {
        \App\Enums\User\UserRole::Agent => redirect()->route('agent.dashboard'),
        \App\Enums\User\UserRole::Admin, \App\Enums\User\UserRole::SuperAdmin => redirect()->route('admin.dashboard'),
        \App\Enums\User\UserRole::Buyer => redirect()->route('buyer.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/properties', \App\Livewire\Property\PropertySearch::class)->name('properties.index');
Route::get('/properties/{property}', \App\Livewire\Property\PropertyDetail::class)->name('properties.show');
Route::get('/agents', \App\Livewire\Agent\Directory::class)->name('agents.index');
Route::get('/agents/{user}', \App\Livewire\Agent\PublicProfile::class)->name('agents.show');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Property\AgentDashboard::class)->name('dashboard');
    Route::redirect('/profile', '/profile')->name('profile.edit');
    Route::get('/properties', \App\Livewire\Property\ManageProperties::class)->name('properties.index');
    Route::get('/properties/create', \App\Livewire\Property\PropertyForm::class)->name('properties.create');
    Route::get('/properties/{property}/edit', \App\Livewire\Property\PropertyForm::class)->name('properties.edit');
    Route::get('/inquiries', \App\Livewire\Agent\InquiryInbox::class)->name('inquiries.index');
    Route::get('/properties/{property}/feature/cancel', function (\App\Models\Property $property) {
        $sessionId = request()->query('session_id');

        if ($sessionId) {
            \App\Models\Payment::where('property_id', $property->id)
                ->where('stripe_checkout_session_id', $sessionId)
                ->where('status', \App\Enums\Payment\PaymentStatus::Pending)
                ->update(['status' => \App\Enums\Payment\PaymentStatus::Cancelled]);
        }

        session()->flash('error', 'Payment cancelled — your listing was not featured. You can try again anytime from My Listings.');

        return redirect()->route('agent.properties.index');
    })->name('properties.feature.cancel');
    Route::get('/properties/{property}/feature/success', \App\Livewire\Agent\FeatureListingSuccess::class)->name('properties.feature.success');
    Route::get('/subscriptions', \App\Livewire\Agent\SubscriptionPlans::class)->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}/success', \App\Livewire\Agent\SubscriptionSuccess::class)->name('subscriptions.success');
    Route::get('/subscriptions/{subscription}/cancel', function (\App\Models\AgentSubscription $subscription) {
        abort_unless($subscription->agent_id === auth()->id(), 403);

        $sessionId = request()->query('session_id');

        if ($sessionId) {
            \App\Models\AgentSubscription::where('id', $subscription->id)
                ->where('stripe_checkout_session_id', $sessionId)
                ->where('status', \App\Enums\Subscription\AgentSubscriptionStatus::Pending)
                ->update(['status' => \App\Enums\Subscription\AgentSubscriptionStatus::Cancelled]);
        }

        session()->flash('error', 'Payment cancelled — your subscription was not activated. You can try again anytime.');

        return redirect()->route('agent.subscriptions.index');
    })->name('subscriptions.cancel');
});

Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/moderation', \App\Livewire\Admin\ModerationQueue::class)->name('moderation.index');
    Route::get('/agents', \App\Livewire\Admin\AgentVerification::class)->name('agents.index');
    Route::get('/users', \App\Livewire\Admin\UserManagement::class)->name('users.index');
    Route::get('/payments', \App\Livewire\Admin\PaymentsIndex::class)->name('payments.index');
    Route::get('/refund-requests', \App\Livewire\Admin\RefundRequestsQueue::class)->name('refund-requests.index');
    Route::get('/settings', \App\Livewire\Admin\SiteSettings::class)->name('settings.edit');
    Route::get('/categories', \App\Livewire\Admin\PropertyCategoryManagement::class)->name('categories.index');
    Route::get('/regions', \App\Livewire\Admin\RegionManagement::class)->name('regions.index');
    Route::get('/featured-tiers', \App\Livewire\Admin\FeaturedTierManagement::class)->name('featured-tiers.index');
    Route::get('/subscription-plans', \App\Livewire\Admin\SubscriptionPlanManagement::class)->name('subscription-plans.index');
    Route::get('/reports', \App\Livewire\Admin\ReportsQueue::class)->name('reports.index');
    Route::get('/properties', \App\Livewire\Admin\PropertyManagement::class)->name('properties.index');
    Route::get('/properties/{property}/edit', \App\Livewire\Property\PropertyForm::class)->name('properties.edit');
    Route::get('/analytics', \App\Livewire\Admin\Analytics::class)->name('analytics');
    Route::get('/contact-messages', \App\Livewire\Admin\ContactMessagesQueue::class)->name('contact-messages.index');
    Route::get('/subscribers', \App\Livewire\Admin\SubscribersIndex::class)->name('subscribers.index');
});

// Admin-account management (creating new Admins) is restricted to Super Admins only.
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/create-admin', \App\Livewire\Admin\CreateAdmin::class)->name('create-admin');
});

Route::middleware(['auth', 'role:buyer'])->group(function () {
    Route::get('/buyer/dashboard', \App\Livewire\Buyer\Dashboard::class)->name('buyer.dashboard');
    Route::get('/favorites', \App\Livewire\Buyer\FavoritesList::class)->name('favorites.index');
    Route::get('/saved-searches', \App\Livewire\Buyer\SavedSearchList::class)->name('saved-searches.index');
    Route::get('/compare', \App\Livewire\Buyer\ComparePage::class)->name('compare.index');
});
