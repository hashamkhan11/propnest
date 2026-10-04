<?php

use App\Enums\User\UserRole;
use App\Http\Controllers\Agent\CancelFeatureCheckoutController;
use App\Http\Controllers\Agent\CancelSubscriptionCheckoutController;
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
    Route::get('/properties/{property}/feature/cancel', CancelFeatureCheckoutController::class)->name('properties.feature.cancel');
    Route::get('/properties/{property}/feature/success', FeatureListingSuccess::class)->name('properties.feature.success');
    Route::get('/subscriptions', SubscriptionPlans::class)->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}/success', SubscriptionSuccess::class)->name('subscriptions.success');
    Route::get('/subscriptions/{subscription}/cancel', CancelSubscriptionCheckoutController::class)->name('subscriptions.cancel');
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
