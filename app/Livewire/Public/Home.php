<?php

namespace App\Livewire\Public;

use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Enums\User\AgentVerificationStatus;
use App\Models\AgentProfile;
use App\Models\Property;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Home extends Component
{
    public string $location = '';

    public string $purpose = '';

    public function search(): void
    {
        if ($this->location === '' && $this->purpose === '') {
            $this->addError('location', 'Enter a location or keyword, or choose For Sale / For Rent, to search.');

            return;
        }

        $this->redirect(route('properties.index', array_filter([
            'location' => $this->location,
            'purpose' => $this->purpose,
        ])), navigate: true);
    }

    public function render()
    {
        $publishedBase = Property::query()->where('status', PropertyStatus::Published);

        $propertyTypeCounts = (clone $publishedBase)
            ->selectRaw('property_type, count(*) as total')
            ->groupBy('property_type')
            ->pluck('total', 'property_type');

        $cityCounts = (clone $publishedBase)
            ->selectRaw('city, count(*) as total')
            ->groupBy('city')
            ->orderByDesc('total')
            ->take(8)
            ->pluck('total', 'city');

        return view('livewire.public.home', [
            'featuredProperties' => (clone $publishedBase)->with(['coverImage', 'images'])->where('is_featured', true)->latest()->take(6)->get(),
            'latestProperties' => (clone $publishedBase)->with(['coverImage', 'images'])->orderBy('is_featured', 'desc')->latest()->take(8)->get(),
            'purposes' => PropertyPurpose::cases(),
            'propertyTypes' => array_filter(PropertyType::cases(), fn (PropertyType $type) => ($propertyTypeCounts[$type->value] ?? 0) > 0),
            'propertyTypeCounts' => $propertyTypeCounts,
            'cityCounts' => $cityCounts,
            'propertyCount' => (clone $publishedBase)->count(),
            'cityCount' => (clone $publishedBase)->distinct()->count('city'),
            'agentCount' => AgentProfile::query()->where('verification_status', AgentVerificationStatus::Verified)->count(),
            'featuredAgents' => AgentProfile::query()
                ->with('user')
                ->where('verification_status', AgentVerificationStatus::Verified)
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }
}
