<?php

namespace App\Livewire\Admin;

use App\Enums\Property\PropertyStatus;
use App\Jobs\MatchSavedSearchesForProperty;
use App\Jobs\NotifyAgentOfPulledForReview;
use App\Jobs\NotifyAgentOfRejection;
use App\Models\Property;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Moderation Queue'])]
class ModerationQueue extends Component
{
    use WithPagination;

    #[Url]
    public string $keyword = '';

    public ?int $rejectingPropertyId = null;

    public string $rejectionReason = '';

    public function updating(string $name): void
    {
        if ($name !== 'page') {
            $this->resetPage();
        }
    }

    public function startReject(int $propertyId): void
    {
        $this->rejectingPropertyId = $propertyId;
        $this->rejectionReason = '';
    }

    public function cancelReject(): void
    {
        $this->rejectingPropertyId = null;
        $this->rejectionReason = '';
    }

    public function reject(Property $property): void
    {
        $this->validate(['rejectionReason' => 'required|string|max:1000']);

        $property->update([
            'status' => PropertyStatus::Rejected,
            'rejection_reason' => $this->rejectionReason,
        ]);

        NotifyAgentOfRejection::dispatch($property, $this->rejectionReason);

        $this->rejectingPropertyId = null;
        $this->rejectionReason = '';

        session()->flash('success', 'Listing rejected and agent notified.');
    }

    public function pullForReview(Property $property): void
    {
        $property->update(['status' => PropertyStatus::PendingReview]);

        NotifyAgentOfPulledForReview::dispatch($property);

        session()->flash('success', 'Listing pulled for review and agent notified.');
    }

    public function approve(Property $property): void
    {
        $property->update(['status' => PropertyStatus::Published, 'has_been_published' => true]);

        MatchSavedSearchesForProperty::dispatch($property);

        session()->flash('success', 'Listing approved and republished.');
    }

    public function render()
    {
        $published = Property::query()
            ->where('status', PropertyStatus::Published)
            ->when($this->keyword !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->keyword}%")
                        ->orWhereHas('agent', fn ($a) => $a->where('name', 'like', "%{$this->keyword}%"));
                });
            })
            ->with('agent')
            ->latest()
            ->paginate(10);

        $pendingReview = Property::query()
            ->where('status', PropertyStatus::PendingReview)
            ->with('agent')
            ->latest()
            ->get();

        return view('livewire.admin.moderation-queue', [
            'published' => $published,
            'pendingReview' => $pendingReview,
        ]);
    }
}
