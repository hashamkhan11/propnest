<?php

namespace App\Livewire\Admin;

use App\Enums\Property\PropertyStatus;
use App\Enums\Report\ReportStatus;
use App\Jobs\NotifyAgentOfRejection;
use App\Models\Report;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Listing Reports'])]
class ReportsQueue extends Component
{
    public ?int $resolvingReportId = null;

    public bool $alsoReject = false;

    public string $rejectionReason = '';

    public function startResolve(int $reportId): void
    {
        $this->resolvingReportId = $reportId;
        $this->alsoReject = false;
        $this->rejectionReason = '';
    }

    public function cancelResolve(): void
    {
        $this->resolvingReportId = null;
    }

    public function dismiss(Report $report): void
    {
        $report->update([
            'status' => ReportStatus::Dismissed,
            'resolved_by_user_id' => auth()->id(),
            'resolved_at' => now(),
        ]);

        session()->flash('success', 'Report dismissed.');
    }

    public function resolve(Report $report): void
    {
        if ($this->alsoReject) {
            $this->validate(['rejectionReason' => 'required|string|max:1000']);

            $property = $report->property;

            $property->update([
                'status' => PropertyStatus::Rejected,
                'rejection_reason' => $this->rejectionReason,
            ]);

            NotifyAgentOfRejection::dispatch($property, $this->rejectionReason);
        }

        $report->update([
            'status' => ReportStatus::ActionTaken,
            'resolved_by_user_id' => auth()->id(),
            'resolved_at' => now(),
        ]);

        $this->resolvingReportId = null;

        session()->flash('success', 'Report resolved.');
    }

    public function render()
    {
        return view('livewire.admin.reports-queue', [
            'reports' => Report::with(['property', 'reportedBy'])
                ->where('status', ReportStatus::Pending)
                ->latest()
                ->paginate(15),
        ]);
    }
}
