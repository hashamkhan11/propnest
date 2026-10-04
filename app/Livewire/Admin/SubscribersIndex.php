<?php

namespace App\Livewire\Admin;

use App\Models\Subscriber;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Subscribers'])]
class SubscribersIndex extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.subscribers-index', [
            'subscribers' => Subscriber::latest()->paginate(15),
        ]);
    }
}
