<x-layouts.customer :title="'Profil Akun - Pixel Komunika'">
    <x-layout.app-page>
        <x-storefront.breadcrumb
            :items="[
                ['label' => 'Akun Saya', 'href' => route('account.dashboard')],
                ['label' => 'Profil', 'href' => null],
            ]"
        />

        <x-ui.page-header
            eyebrow="Profil"
            :title="$user->name"
            :description="$user->email.' • '.($user->phone ?? 'No phone')"
        />

        <x-ui.section-card
            title="Profil pelanggan"
            description="Data ini dipakai untuk identitas akun dan persiapan checkout."
        >
            <form method="POST" action="{{ route('account.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <flux:input
                    id="name"
                    name="name"
                    label="Nama"
                    value="{{ old('name', $user->name) }}"
                    required
                />

                <flux:input
                    id="phone"
                    name="phone"
                    label="Nomor telepon"
                    value="{{ old('phone', $user->phone) }}"
                    required
                />

                <flux:input
                    id="business_name"
                    name="business_name"
                    label="Nama usaha"
                    value="{{ old('business_name', $user->customerProfile?->business_name) }}"
                />

                <flux:button type="submit" variant="primary" color="amber">
                    Simpan profil
                </flux:button>
            </form>
        </x-ui.section-card>
    </x-layout.app-page>
</x-layouts.customer>
