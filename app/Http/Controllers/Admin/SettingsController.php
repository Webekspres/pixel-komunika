<?php

namespace App\Http\Controllers\Admin;

use App\Domains\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\StoreCourierRate;
use App\Models\StoreProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    protected function backToTab(Request $request, string $message): RedirectResponse
    {
        return back()->withFragment($request->string('tab')->toString())->with('status', $message);
    }

    public function index(): View
    {
        $store = StoreProfile::active();

        $auditTrail = $store
            ? AuditLog::query()
                ->where('auditable_type', StoreProfile::class)
                ->where('auditable_id', $store->id)
                ->where('action', 'STORE_PROFILE_UPDATED')
                ->orderByDesc('id')
                ->limit(20)
                ->get()
            : collect();

        return view('admin.settings.index', [
            'store' => $store ?? new StoreProfile,
            'bankAccounts' => BankAccount::query()
                ->orderByDesc('is_active')
                ->orderBy('id')
                ->get(),
            'courierRates' => StoreCourierRate::query()
                ->orderByDesc('is_active')
                ->orderBy('area_name')
                ->get(),
            'auditTrail' => $auditTrail,
        ]);
    }

    public function updateStoreProfile(Request $request, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['sometimes', 'required', 'string', 'max:191'],
            'address' => ['sometimes', 'required', 'string'],
            'contact_number' => ['sometimes', 'required', 'string', 'max:32'],
            'company_name' => ['nullable', 'string', 'max:191'],
            'company_npwp' => ['sometimes', 'required', 'string', 'max:32'],
            'partai_minimum_quantity' => ['sometimes', 'required', 'integer', 'min:1', 'max:100000'],
            'origin_biteship_area_id' => ['nullable', 'string', 'max:191'],
            'origin_postal_code' => ['nullable', 'string', 'max:16'],
        ]);

        $store = StoreProfile::active() ?? new StoreProfile;
        $oldValues = $store->exists
            ? $store->only(['store_name', 'address', 'contact_number', 'company_name', 'company_npwp', 'partai_minimum_quantity', 'origin_biteship_area_id', 'origin_postal_code'])
            : [];

        $store->fill([...$validated, 'is_active' => true])->save();

        if (array_key_exists('partai_minimum_quantity', $validated)) {
            $audit->log('STORE_PROFILE_UPDATED', $store, $request->user(), $oldValues, $store->only(['partai_minimum_quantity']));
        }

        return $this->backToTab($request, 'Pengaturan toko berhasil disimpan.');
    }

    public function storeBankAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:64', 'unique:bank_accounts,account_number'],
            'account_holder' => ['required', 'string', 'max:191'],
            'instructions' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        BankAccount::query()->create([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return $this->backToTab($request, 'Rekening bank berhasil ditambahkan.');
    }

    public function updateBankAccount(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:64', Rule::unique('bank_accounts', 'account_number')->ignore($bankAccount->id)],
            'account_holder' => ['required', 'string', 'max:191'],
            'instructions' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $bankAccount->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return $this->backToTab($request, 'Rekening bank berhasil diperbarui.');
    }

    public function destroyBankAccount(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        if ($bankAccount->payments()->exists()) {
            return back()->withErrors(['bank_account' => 'Rekening sudah dipakai transaksi dan tidak dapat dihapus.']);
        }

        $bankAccount->delete();

        return $this->backToTab($request, 'Rekening bank berhasil dihapus.');
    }

    public function storeCourierRate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'area_code' => ['nullable', 'string', 'max:32'],
            'area_name' => ['required', 'string', 'max:191'],
            'rate_amount' => ['required', 'numeric', 'min:0'],
            'eta_text' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        StoreCourierRate::query()->create([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return $this->backToTab($request, 'Tarif kurir toko berhasil ditambahkan.');
    }

    public function updateCourierRate(Request $request, StoreCourierRate $courierRate): RedirectResponse
    {
        $validated = $request->validate([
            'area_code' => ['nullable', 'string', 'max:32'],
            'area_name' => ['required', 'string', 'max:191'],
            'rate_amount' => ['required', 'numeric', 'min:0'],
            'eta_text' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $courierRate->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return $this->backToTab($request, 'Tarif kurir toko berhasil diperbarui.');
    }

    public function destroyCourierRate(Request $request, StoreCourierRate $courierRate): RedirectResponse
    {
        $courierRate->delete();

        return $this->backToTab($request, 'Tarif kurir toko berhasil dihapus.');
    }
}
