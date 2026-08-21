<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $user = $request->user();
        $isDefault = $user->addresses()->doesntExist() || $request->boolean('is_default');

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
            ...$validated,
            'is_default' => $isDefault,
        ]);

        return back();
    }

    public function update(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $validated = $this->validated($request);
        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            $request->user()->addresses()
                ->whereKeyNot($address->id)
                ->update(['is_default' => false]);
        }

        $address->update([
            ...$validated,
            'is_default' => $isDefault,
        ]);

        return back();
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $request->user()->addresses()
                ->oldest('id')
                ->limit(1)
                ->update(['is_default' => true]);
        }

        return back();
    }

    public function setDefault(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $request->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:100'],
            'recipient_name' => ['required', 'string', 'max:150'],
            'recipient_phone' => ['required', 'string', 'max:32'],
            'address_line' => ['required', 'string'],
            'province_name' => ['required', 'string', 'max:100'],
            'city_name' => ['required', 'string', 'max:100'],
            'district_name' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:16'],
        ]);
    }
}
