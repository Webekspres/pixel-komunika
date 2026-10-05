<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['phone' => User::normalizePhone($request->input('phone'))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'business_name' => ['nullable', 'string', 'max:191'],
            'phone' => ['required', 'string', 'regex:/^08\d{8,12}$/', 'unique:users,phone'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:191', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::query()->create([
            'role_id' => Role::query()->where('code', Role::CUSTOMER)->value('id'),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
        ]);

        $user->customerProfile()->create([
            'business_name' => $validated['business_name'] ?: null,
            'verification_status' => CustomerProfile::PENDING,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account.dashboard');
    }
}
