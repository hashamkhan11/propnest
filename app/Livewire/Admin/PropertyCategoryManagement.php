<?php

namespace App\Livewire\Admin;

use App\Models\PropertyCategory;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Categories'])]
class PropertyCategoryManagement extends Component
{
    public string $name = '';

    public ?int $editingId = null;

    public function startCreate(): void
    {
        $this->editingId = null;
        $this->name = '';
    }

    public function startEdit(PropertyCategory $category): void
    {
        $this->editingId = $category->id;
        $this->name = $category->name;
    }

    public function save(): void
    {
        $this->validate(['name' => 'required|string|max:255']);

        if ($this->editingId) {
            PropertyCategory::whereKey($this->editingId)->update(['name' => $this->name]);
            session()->flash('success', 'Category updated.');
        } else {
            PropertyCategory::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'is_active' => true,
            ]);
            session()->flash('success', 'Category created.');
        }

        $this->name = '';
        $this->editingId = null;
    }

    public function toggleActive(PropertyCategory $category): void
    {
        $category->update(['is_active' => ! $category->is_active]);
    }

    public function delete(PropertyCategory $category): void
    {
        if ($category->properties()->exists()) {
            session()->flash('error', 'Cannot delete a category that is in use — deactivate it instead.');

            return;
        }

        $category->delete();

        session()->flash('success', 'Category deleted.');
    }

    public function render()
    {
        return view('livewire.admin.property-category-management', [
            'categories' => PropertyCategory::withCount('properties')->orderBy('name')->get(),
        ]);
    }
}
