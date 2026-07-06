<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $filterRole = '';

    // Modal control states
    public $showCreateModal = false;

    public $showEditModal = false;

    public $showDeleteModal = false;

    // Form fields
    public $name = '';

    public $email = '';

    public $password = '';

    public $role = 'employee';

    public $selectedUserId = null;

    protected $queryString = ['search', 'filterRole'];

    public function mount()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['super_admin', 'hrd', 'finance', 'manager', 'employee'])],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'role.required' => 'Role pengguna wajib dipilih.',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        $this->closeCreateModal();
        $this->dispatch('toast', type: 'success', message: 'Pengguna baru berhasil ditambahkan!');
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->selectedUserId = $id;
        $user = User::findOrFail($id);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateUser()
    {
        $user = User::findOrFail($this->selectedUserId);

        $this->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => ['required', Rule::in(['super_admin', 'hrd', 'finance', 'manager', 'employee'])],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'role.required' => 'Role pengguna wajib dipilih.',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->closeEditModal();
        $this->dispatch('toast', type: 'success', message: 'Data pengguna berhasil diperbarui!');
    }

    public function openDeleteModal($id)
    {
        if (auth()->id() == $id) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak dapat menghapus akun Anda sendiri!');

            return;
        }

        $this->selectedUserId = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedUserId = null;
    }

    public function deleteUser()
    {
        if (auth()->id() == $this->selectedUserId) {
            $this->closeDeleteModal();
            $this->dispatch('toast', type: 'error', message: 'Anda tidak dapat menghapus akun Anda sendiri!');

            return;
        }

        $user = User::findOrFail($this->selectedUserId);
        $user->delete();

        $this->closeDeleteModal();
        $this->dispatch('toast', type: 'success', message: 'Pengguna berhasil dihapus dari sistem!');
    }

    protected function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'employee';
        $this->selectedUserId = null;
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterRole, function ($query) {
                $query->where('role', $this->filterRole);
            })
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users' => $users,
        ])->layout('layouts.app');
    }
}
