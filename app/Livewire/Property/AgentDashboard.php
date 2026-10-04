<?php

namespace App\Livewire\Property;

use App\Enums\Property\PropertyStatus;
use App\Models\Inquiry;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AgentDashboard extends Component
{
    public function render()
    {
        $base = Property::query()->where('agent_id', Auth::id());

        return view('livewire.property.agent-dashboard', [
            'totalCount' => (clone $base)->count(),
            'publishedCount' => (clone $base)->where('status', PropertyStatus::Published)->count(),
            'draftCount' => (clone $base)->where('status', PropertyStatus::Draft)->count(),
            'soldRentedCount' => (clone $base)->whereIn('status', [PropertyStatus::Sold, PropertyStatus::Rented])->count(),
            'unreadInquiriesCount' => Inquiry::where('agent_id', Auth::id())->whereNull('read_at')->count(),
            'totalViewsCount' => (clone $base)->sum('views_count'),
            'totalInquiriesCount' => Inquiry::where('agent_id', Auth::id())->count(),
            'profileIncomplete' => blank(Auth::user()->agentProfile?->agency_name)
                || blank(Auth::user()->agentProfile?->phone)
                || blank(Auth::user()->agentProfile?->bio),
        ]);
    }
}
