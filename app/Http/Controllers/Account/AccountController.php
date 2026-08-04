<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->load(['customerProfile', 'addresses']);

        return view('account.dashboard', [
            'user' => $user,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone,'.$request->user()->id],
            'business_name' => ['nullable', 'string', 'max:191'],
        ]);

        $request->user()->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $request->user()->customerProfile()?->update([
            'business_name' => $validated['business_name'] ?: null,
        ]);

        return back();
    }
}
