<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email = '';

    public $password = '';

    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            $user = Auth::user();
            if (in_array($user->role, ['super_admin', 'hrd', 'finance', 'manager'])) {
                return redirect()->intended(route('admin.overview'));
            } elseif ($user->role === 'employee') {
                return redirect()->intended(route('employee.dashboard'));
            }

            return redirect()->to('/');
        }

        $this->addError('email', 'Kredensial yang Anda masukkan tidak cocok dengan data kami.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest');
    }
}
