<x-layouts.app :title="'Admin Dashboard - Pixel Komunika'">
    <x-layout.admin-page
        title="Admin dashboard"
        description="Workspace awal untuk memantau pendaftaran customer dan pergerakan order tanpa keluar dari application shell."
    >
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-ui.stat-card label="Total customer" :value="(string) $customerCount" icon="users" variant="admin" />
            <x-ui.stat-card label="Pending review" :value="(string) $pendingCustomerCount" icon="badge-check" variant="admin" />
            <x-ui.stat-card label="Total order" :value="(string) $orderCount" icon="clipboard-list" variant="admin" />
            <x-ui.stat-card label="Perlu follow up" :value="(string) $unpaidOrderCount" icon="search" variant="admin" />
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <x-ui.section-card
                title="Moderasi customer"
                description="Masuk ke daftar customer untuk approval, suspend, atau review detail usaha."
                variant="admin"
            >
                <div class="space-y-4">
                    <p class="text-sm leading-relaxed text-zinc-600">
                        Application shell sekarang menjadi baseline semua halaman admin, jadi modul review customer dan order management tidak lagi loncat ke storefront.
                    </p>

                    <a
                        href="{{ route('admin.customers.index') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-brand-black px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-black/88"
                    >
                        Buka customer management
                    </a>
                </div>
            </x-ui.section-card>

            <x-ui.section-card
                title="Operasional order"
                description="Lanjutkan ke workflow pembayaran, status order, dan tindak lanjut pengiriman."
                variant="admin"
            >
                <div class="space-y-4">
                    <p class="text-sm leading-relaxed text-zinc-600">
                        Gunakan halaman order untuk verifikasi bukti bayar, update status paid atau shipped, dan memastikan setiap transaksi tetap berada di context admin.
                    </p>

                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-brand-yellow px-4 py-3 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-soft"
                    >
                        Buka manajemen pesanan
                    </a>
                </div>
            </x-ui.section-card>
        </div>
    </x-layout.admin-page>
</x-layouts.app>
