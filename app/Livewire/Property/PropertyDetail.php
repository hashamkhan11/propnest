<?php

namespace App\Livewire\Property;

use App\Enums\Report\ReportReason;
use App\Enums\Report\ReportStatus;
use App\Enums\User\UserRole;
use App\Models\Property;
use App\Models\Report;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class PropertyDetail extends Component
{
    public Property $property;

    public bool $showReportForm = false;

    public string $reportReason = '';

    public string $reportDetails = '';

    public function mount(Property $property): void
    {
        $this->authorize('view', $property);

        if (auth()->id() !== $property->agent_id) {
            $property->increment('views_count');
        }

        $this->property = $property->load(['coverImage', 'images', 'amenities', 'agent.agentProfile']);
    }

    public function toggleReportForm(): void
    {
        $this->showReportForm = ! $this->showReportForm;
    }

    public function submitReport(): void
    {
        abort_unless(auth()->check(), 403);
        abort_if(auth()->user()->role === UserRole::Agent, 403);

        $existing = Report::where('property_id', $this->property->id)
            ->where('reported_by_user_id', auth()->id())
            ->where('status', ReportStatus::Pending)
            ->exists();

        if ($existing) {
            session()->flash('error', 'You already have a pending report for this listing.');
            $this->showReportForm = false;

            return;
        }

        $this->validate([
            'reportReason' => 'required|in:'.implode(',', array_column(ReportReason::cases(), 'value')),
            'reportDetails' => 'nullable|string|max:1000',
        ]);

        Report::create([
            'property_id' => $this->property->id,
            'reported_by_user_id' => auth()->id(),
            'reason' => $this->reportReason,
            'details' => $this->reportDetails,
        ]);

        $this->showReportForm = false;
        $this->reportReason = '';
        $this->reportDetails = '';

        session()->flash('success', 'Thanks — this listing has been reported to our team.');
    }

    public function render()
    {
        return view('livewire.property.property-detail', [
            'reportReasons' => ReportReason::cases(),
        ]);
    }
}
