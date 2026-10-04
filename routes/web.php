<?php

use App\Enums\Payment\PaymentStatus;
use App\Enums\Subscription\AgentSubscriptionStatus;
use App\Enums\User\UserRole;
use App\Livewire\Admin\AgentVerification;
use App\Livewire\Admin\Analytics;
use App\Livewire\Admin\ContactMessagesQueue;
use App\Livewire\Admin\CreateAdmin;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\FeaturedTierManagement;
use App\Livewire\Admin\ModerationQueue;
use App\Livewire\Admin\PaymentsIndex;
use App\Livewire\Admin\PropertyCategoryManagement;
use App\Livewire\Admin\PropertyManagement;
use App\Livewire\Admin\RefundRequestsQueue;
use App\Livewire\Admin\RegionManagement;
use App\Livewire\Admin\ReportsQueue;
use App\Livewire\Admin\SiteSettings;
use App\Livewire\Admin\SubscribersIndex;
use App\Livewire\Admin\SubscriptionPlanManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Agent\Directory;
use App\Livewire\Agent\FeatureListingSuccess;
use App\Livewire\Agent\InquiryInbox;
use App\Livewire\Agent\PublicProfile;
use App\Livewire\Agent\SubscriptionPlans;
use App\Livewire\Agent\SubscriptionSuccess;
use App\Livewire\Buyer\ComparePage;
use App\Livewire\Buyer\FavoritesList;
use App\Livewire\Buyer\SavedSearchList;
use App\Livewire\Property\AgentDashboard;
use App\Livewire\Property\ManageProperties;
use App\Livewire\Property\PropertyDetail;
use App\Livewire\Property\PropertyForm;
use App\Livewire\Property\PropertySearch;
use App\Livewire\Public\Home;
use App\Models\AgentSubscription;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');

Route::get('dashboard', function () {
    return match (auth()->user()->role) {
        UserRole::Agent => redirect()->route('agent.dashboard'),
        UserRole::Admin, UserRole::SuperAdmin => redirect()->route('admin.dashboard'),
        UserRole::Buyer => redirect()->route('buyer.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/properties', PropertySearch::class)->name('properties.index');
Route::get('/properties/{property}', PropertyDetail::class)->name('properties.show');
Route::get('/agents', Directory::class)->name('agents.index');
Route::get('/agents/{user}', PublicProfile::class)->name('agents.show');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', AgentDashboard::class)->name('dashboard');
    Route::redirect('/profile', '/profile')->name('profile.edit');
    Route::get('/properties', ManageProperties::class)->name('properties.index');
    Route::get('/properties/create', PropertyForm::class)->name('properties.create');
    Route::get('/properties/{property}/edit', PropertyForm::class)->name('properties.edit');
    Route::get('/inquiries', InquiryInbox::class)->name('inquiries.index');
    Route::get('/properties/{property}/feature/cancel', function (Property $property) {
        $sessionId = request()->query('session_id');

        if ($sessionId) {
            Payment::where('property_id', $property->id)
                ->where('stripe_checkout_session_id', $sessionId)
                ->where('status', PaymentStatus::Pending)
                ->update(['status' => PaymentStatus::Cancelled]);
        }

        session()->flash('error', 'Payment cancelled — your listing was not featured. You can try again anytime from My Listings.');

        return redirect()->route('agent.properties.index');
    })->name('properties.feature.cancel');
    Route::get('/properties/{property}/feature/success', FeatureListingSuccess::class)->name('properties.feature.success');
    Route::get('/subscriptions', SubscriptionPlans::class)->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}/success', SubscriptionSuccess::class)->name('subscriptions.success');
    Route::get('/subscriptions/{subscription}/cancel', function (AgentSubscription $subscription) {
        abort_unless($subscription->agent_id === auth()->id(), 403);

        $sessionId = request()->query('session_id');

        if ($sessionId) {
            AgentSubscription::where('id', $subscription->id)
                ->where('stripe_checkout_session_id', $sessionId)
                ->where('status', AgentSubscriptionStatus::Pending)
                ->update(['status' => AgentSubscriptionStatus::Cancelled]);
        }

        session()->flash('error', 'Payment cancelled — your subscription was not activated. You can try again anytime.');

        return redirect()->route('agent.subscriptions.index');
    })->name('subscriptions.cancel');
});

Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/moderation', ModerationQueue::class)->name('moderation.index');
    Route::get('/agents', AgentVerification::class)->name('agents.index');
    Route::get('/users', UserManagement::class)->name('users.index');
    Route::get('/payments', PaymentsIndex::class)->name('payments.index');
    Route::get('/refund-requests', RefundRequestsQueue::class)->name('refund-requests.index');
    Route::get('/settings', SiteSettings::class)->name('settings.edit');
    Route::get('/categories', PropertyCategoryManagement::class)->name('categories.index');
    Route::get('/regions', RegionManagement::class)->name('regions.index');
    Route::get('/featured-tiers', FeaturedTierManagement::class)->name('featured-tiers.index');
    Route::get('/subscription-plans', SubscriptionPlanManagement::class)->name('subscription-plans.index');
    Route::get('/reports', ReportsQueue::class)->name('reports.index');
    Route::get('/properties', PropertyManagement::class)->name('properties.index');
    Route::get('/properties/{property}/edit', PropertyForm::class)->name('properties.edit');
    Route::get('/analytics', Analytics::class)->name('analytics');
    Route::get('/contact-messages', ContactMessagesQueue::class)->name('contact-messages.index');
    Route::get('/subscribers', SubscribersIndex::class)->name('subscribers.index');
});

// Admin-account management (creating new Admins) is restricted to Super Admins only.
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/create-admin', CreateAdmin::class)->name('create-admin');
});

Route::middleware(['auth', 'role:buyer'])->group(function () {
    Route::get('/buyer/dashboard', App\Livewire\Buyer\Dashboard::class)->name('buyer.dashboard');
    Route::get('/favorites', FavoritesList::class)->name('favorites.index');
    Route::get('/saved-searches', SavedSearchList::class)->name('saved-searches.index');
    Route::get('/compare', ComparePage::class)->name('compare.index');
});
