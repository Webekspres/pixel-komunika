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
    protected const QUANTITY_FIELDS = [
        'partai_minimum_quantity' => 'Minimum partai',
        'minimum_order_quantity' => 'Minimal pembelian',
    ];

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
                ->limit(100)
                ->get()
                // Tab partai hanya menampilkan perubahan kuantitas; identitas/NPWP ada di Audit Log.
                ->filter(fn (AuditLog $log) => array_intersect_key((array) $log->new_values, self::QUANTITY_FIELDS) !== [])
                ->take(20)
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
            'minimum_order_quantity' => ['sometimes', 'required', 'integer', 'min:1', 'max:100000'],
            'origin_biteship_area_id' => ['nullable', 'string', 'max:191'],
            'origin_biteship_label' => ['nullable', 'string', 'max:255'],
            'origin_postal_code' => ['nullable', 'string', 'max:16'],
        ]);

        $store = StoreProfile::active() ?? new StoreProfile;
        $oldValues = $store->exists
            ? $store->only(['store_name', 'address', 'contact_number', 'company_name', 'company_npwp', 'partai_minimum_quantity', 'minimum_order_quantity', 'origin_biteship_area_id', 'origin_biteship_label', 'origin_postal_code'])
            : [];

        $store->fill([...$validated, 'is_active' => true])->save();

        // Identitas/NPWP tercetak di invoice, jadi setiap tab yang disimpan diaudit.
        $audit->log('STORE_PROFILE_UPDATED', $store, $request->user(), $oldValues, $store->only(array_keys($validated)));

        return $this->backToTab($request, 'Pengaturan toko berhasil disimpan.');
    }

    public function storeBankAccount(Request $request, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:64', 'unique:bank_accounts,account_number'],
            'account_holder' => ['required', 'string', 'max:191'],
            'instructions' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $account = BankAccount::query()->create([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);
        $audit->log('BANK_ACCOUNT_CREATED', $account, $request->user(), null, $account->only(array_keys($validated)));

        return $this->backToTab($request, 'Rekening bank berhasil ditambahkan.');
    }

    public function updateBankAccount(Request $request, BankAccount $bankAccount, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:64', Rule::unique('bank_accounts', 'account_number')->ignore($bankAccount->id)],
            'account_holder' => ['required', 'string', 'max:191'],
            'instructions' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $oldValues = $bankAccount->only([...array_keys($validated), 'is_active']);
        $bankAccount->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);
        $audit->log('BANK_ACCOUNT_UPDATED', $bankAccount, $request->user(), $oldValues, $bankAccount->only(array_keys($oldValues)));

        return $this->backToTab($request, 'Rekening bank berhasil diperbarui.');
    }

    public function destroyBankAccount(Request $request, BankAccount $bankAccount, AuditLogger $audit): RedirectResponse
    {
        if ($bankAccount->payments()->exists()) {
            return back()->withErrors(['bank_account' => 'Rekening sudah dipakai transaksi dan tidak dapat dihapus.']);
        }

        $audit->log('BANK_ACCOUNT_DELETED', $bankAccount, $request->user(), $bankAccount->only(['bank_name', 'account_number', 'account_holder']));
        $bankAccount->delete();

        return $this->backToTab($request, 'Rekening bank berhasil dihapus.');
    }

    public function storeCourierRate(Request $request, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate([
            'area_code' => ['nullable', 'string', 'max:32'],
            'area_name' => ['required', 'string', 'max:191'],
            'rate_amount' => ['required', 'numeric', 'min:0'],
            'eta_text' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $rate = StoreCourierRate::query()->create([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);
        // SRS §15: perubahan konfigurasi kurir wajib diaudit.
        $audit->log('COURIER_RATE_CREATED', $rate, $request->user(), null, $rate->only([...array_keys($validated), 'is_active']));

        return $this->backToTab($request, 'Tarif kurir toko berhasil ditambahkan.');
    }

    public function updateCourierRate(Request $request, StoreCourierRate $courierRate, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate([
            'area_code' => ['nullable', 'string', 'max:32'],
            'area_name' => ['required', 'string', 'max:191'],
            'rate_amount' => ['required', 'numeric', 'min:0'],
            'eta_text' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $oldValues = $courierRate->only([...array_keys($validated), 'is_active']);
        $courierRate->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);
        $audit->log('COURIER_RATE_UPDATED', $courierRate, $request->user(), $oldValues, $courierRate->only(array_keys($oldValues)));

        return $this->backToTab($request, 'Tarif kurir toko berhasil diperbarui.');
    }

    public function destroyCourierRate(Request $request, StoreCourierRate $courierRate, AuditLogger $audit): RedirectResponse
    {
        $audit->log('COURIER_RATE_DELETED', $courierRate, $request->user(), $courierRate->only(['area_code', 'area_name', 'rate_amount']));
        $courierRate->delete();

        return $this->backToTab($request, 'Tarif kurir toko berhasil dihapus.');
    }
}
