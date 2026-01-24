<?php

use Livewire\Component;

new class extends Component
{

    public $name;
    public $email;
    public $password;
    public $role;

    public function create()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:staff,admin',
        ]);

        \App\Models\User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'type' => $this->role,
        ]);

        session()->flash('message', __('User created successfully.'));

        $this->reset(['name', 'email', 'password', 'role']);
    }

    public function render()
    {
        return $this->view()
            ->title(__('Create User'));
    }
};
?>

<div class="flex flex-col gap-10">

    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('users.index') }}">Users</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>User Create</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl" >
        {{ __("Create User") }}
    </flux:heading>

    <div class="flex w-full justify-center items-center">
    <form wire:submit.prevent="create" class="grid grid-cols-2 gap-6 w-2xl">
    <flux:field>
            <flux:label>Name</flux:label>
            <flux:description>This will be publicly displayed.</flux:description>
            <flux:input wire:model="name" />
        <flux:error name="name" />
    </flux:field>

    <flux:field>
            <flux:label>Email</flux:label>
            <flux:description>This will be used for login.</flux:description>
            <flux:input type="email" wire:model="email" />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
            <flux:label>Password</flux:label>
            <flux:description>Must be at least 8 characters.</flux:description>
            <flux:input type="password" wire:model="password" />
        <flux:error name="password" />
    </flux:field>

    <flux:field>
            <flux:label>Role</flux:label>
            <flux:description>Select the user's role.</flux:description>
            <flux:select wire:model="role">
                <option value="">Select role</option>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
            </flux:select>
        <flux:error name="role" />
    </flux:field>

    <flux:button type="submit" variant="primary" class="col-span-2 justify-center">
        {{ __("Create User") }}
    </flux:button>

    </form>
    </div>

</div>
