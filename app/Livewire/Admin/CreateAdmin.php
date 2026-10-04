<?php

namespace App\Livewire\Admin;

use App\Enums\User\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Create Admin'])]
class CreateAdmin extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->role === UserRole::SuperAdmin, 403);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    public function save(): void
    {
        abort_unless(auth()->user()->role === UserRole::SuperAdmin, 403);

        $validated = $this->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 'role' is now fillable on User (added in Task 16 to support validated
        // role selection in public registration), but we set it explicitly here
        // because CreateAdmin needs to assign 'role = Admin', a value that
        // registration's validation would never allow. 'email_verified_at' is
        // excluded from fillable, so it must also be set explicitly.
        $user->role = UserRole::Admin;
        $user->email_verified_at = now();
        $user->save();

        session()->flash('success', 'Admin account created.');

        $this->reset(['name', 'email', 'password', 'password_confirmation']);
    }

    public function render()
    {
        return view('livewire.admin.create-admin');
    }
}
