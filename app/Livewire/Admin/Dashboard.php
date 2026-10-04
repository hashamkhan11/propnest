<?php

namespace App\Livewire\Admin;

use App\Enums\Payment\PaymentStatus;
use App\Enums\Property\PropertyStatus;
use App\Enums\Report\ReportStatus;
use App\Enums\User\UserRole;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Report;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalUsers' => User::count(),
            'totalAgents' => User::where('role', UserRole::Agent)->count(),
            'totalBuyers' => User::where('role', UserRole::Buyer)->count(),
            'totalAdmins' => User::where('role', UserRole::Admin)->count(),
            'pendingReportsCount' => Report::where('status', ReportStatus::Pending)->count(),
            'totalProperties' => Property::count(),
            'featuredCount' => Property::where('is_featured', true)->where('featured_until', '>', now())->count(),
            'pendingReviewCount' => Property::where('status', PropertyStatus::PendingReview)->count(),
            'revenue' => Payment::where('status', PaymentStatus::Completed)->sum('amount') / 100,
            'recentPayments' => Payment::with(['agent', 'property'])->latest()->take(5)->get(),
            'recentProperties' => Property::with('agent')->latest()->take(5)->get(),
            'recentUsers' => User::latest()->take(5)->get(),
        ]);
    }
}
