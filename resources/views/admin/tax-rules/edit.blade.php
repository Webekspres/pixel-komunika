<x-layouts.app :title="'Edit PPh 22 - '.$category->name">
    <x-layout.admin-page
        :title="'PPh 22: '.$category->name"
        description="Atur ambang nilai belanja dan tarif PPh 22 untuk klasifikasi ini."
    >
        <flux:button href="{{ route('admin.tax-rules.index') }}" variant="ghost" size="sm" icon="arrow-left" class="mb-4">
            Kembali ke daftar
        </flux:button>

        <x-ui.section-card title="Aturan per kategori">
            <form method="POST" action="{{ route('admin.tax-rules.update', $category) }}" class="grid max-w-xl gap-4">
                @csrf
                @method('PATCH')

                <flux:input
                    type="number"
                    name="threshold_amount"
                    label="Ambang nilai belanja (Rp)"
                    value="{{ old('threshold_amount', $taxRule->threshold_amount) }}"
                    min="0"
                    step="1"
                    required
                />

                <flux:input
                    type="number"
                    name="rate_percent"
                    label="Tarif PPh 22 (%)"
                    value="{{ old('rate_percent', $taxRule->rate_percent) }}"
                    min="0"
                    max="100"
                    step="0.0001"
                    required
                />

                <label class="flex items-center gap-2 text-sm text-zinc-700">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="rounded border-zinc-300"
                        @checked(old('is_active', $taxRule->is_active))
                    >
                    Aturan aktif
                </label>

                <div class="flex gap-2 pt-2">
                    <flux:button type="submit" variant="primary" color="amber">Simpan</flux:button>
                    <flux:button href="{{ route('admin.tax-rules.index') }}" variant="ghost">Batal</flux:button>
                </div>
            </form>
        </x-ui.section-card>
    </x-layout.admin-page>
</x-layouts.app>
