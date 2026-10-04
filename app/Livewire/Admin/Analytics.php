<?php

namespace App\Livewire\Admin;

use App\Enums\Payment\PaymentStatus;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Analytics'])]
class Analytics extends Component
{
    public function render()
    {
        $since = now()->subMonths(11)->startOfMonth();

        $revenueByMonth = Payment::where('status', PaymentStatus::Completed)
            ->where('created_at', '>=', $since)
            ->selectRaw($this->monthExpression().' as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $usersByMonth = User::where('created_at', '>=', $since)
            ->selectRaw($this->monthExpression().' as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $listingsByStatus = Property::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $listingsByCategory = Property::query()
            ->join('property_categories', 'properties.category_id', '=', 'property_categories.id')
            ->selectRaw('property_categories.name as category_name, COUNT(*) as total')
            ->groupBy('property_categories.name')
            ->orderByDesc('total')
            ->pluck('total', 'category_name');

        $listingsByRegion = Property::query()
            ->join('cities', 'properties.city_id', '=', 'cities.id')
            ->join('regions', 'cities.region_id', '=', 'regions.id')
            ->selectRaw('regions.name as region_name, COUNT(*) as total')
            ->groupBy('regions.name')
            ->orderByDesc('total')
            ->pluck('total', 'region_name');

        $topAgents = Payment::where('payments.status', PaymentStatus::Completed)
            ->join('users', 'payments.agent_id', '=', 'users.id')
            ->selectRaw('users.name as agent_name, SUM(payments.amount) as total_revenue, COUNT(*) as payment_count')
            ->groupBy('users.name')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        $reportCountsByStatus = Report::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $refundTotal = Payment::where('status', PaymentStatus::Refunded)->count();

        return view('livewire.admin.analytics', [
            'revenueByMonth' => $revenueByMonth,
            'usersByMonth' => $usersByMonth,
            'listingsByStatus' => $listingsByStatus,
            'listingsByCategory' => $listingsByCategory,
            'listingsByRegion' => $listingsByRegion,
            'topAgents' => $topAgents,
            'reportCountsByStatus' => $reportCountsByStatus,
            'refundTotal' => $refundTotal,
        ]);
    }

    // Each database spells "year-month of created_at" differently.
    private function monthExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };
    }
}
