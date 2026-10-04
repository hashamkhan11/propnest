<?php

namespace App\Livewire\Admin;

use App\Enums\Payment\PaymentStatus;
use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Property Management'])]
class PropertyManagement extends Component
{
    use WithPagination;

    #[Url]
    public string $keyword = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $categoryFilter = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['keyword', 'statusFilter', 'categoryFilter'], true)) {
            $this->resetPage();
        }
    }

    public function delete(Property $property): void
    {
        $hasBlockingPayment = $property->payments()
            ->whereNotIn('status', [PaymentStatus::Pending, PaymentStatus::Cancelled])
            ->exists();

        if ($hasBlockingPayment) {
            session()->flash('error', 'Cannot delete a listing with payment history — this protects financial records.');

            return;
        }

        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $property->delete();

        session()->flash('success', 'Listing deleted.');
    }

    public function render()
    {
        $properties = Property::query()
            ->when($this->keyword !== '', fn ($q) => $q->where('title', 'like', "%{$this->keyword}%"))
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->categoryFilter !== '', fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->with(['agent', 'category', 'cityRecord'])
            ->latest()
            ->paginate(15);

        return view('livewire.admin.property-management', [
            'properties' => $properties,
            'statuses' => PropertyStatus::cases(),
            'categories' => PropertyCategory::orderBy('name')->get(),
        ]);
    }
}
