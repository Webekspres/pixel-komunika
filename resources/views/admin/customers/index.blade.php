<x-layouts.app :title="'Customer Management - Pixel Komunika'">
    <x-layout.admin-page
        title="Customer management"
        description="Review pendaftaran pelanggan, cek status verifikasi, dan jalankan approval dari satu pola list internal."
    >
        <div class="grid gap-4 md:grid-cols-3">
            <x-ui.stat-card label="Total di halaman ini" :value="(string) $customers->count()" />
            <x-ui.stat-card label="Filter status" :value="$selectedStatus ?: 'Semua'" />
            <x-ui.stat-card label="Keyword" :value="$search !== '' ? $search : '-'" description="Cari nama, email, phone, atau nama usaha." />
        </div>

        <x-ui.filter-bar>
            <form method="GET" class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_220px_auto] lg:items-end">
                <flux:input
                    type="search"
                    name="q"
                    label="Cari customer"
                    value="{{ $search }}"
                    placeholder="Nama, email, phone, usaha"
                    icon="magnifying-glass"
                />

                <flux:select name="status" label="Status">
                    <flux:select.option value="" label="Semua" {{ $selectedStatus === '' ? 'selected' : '' }} />
                    @foreach ($statuses as $status)
                        <flux:select.option value="{{ $status }}" label="{{ $status }}" {{ $selectedStatus === $status ? 'selected' : '' }} />
                    @endforeach
                </flux:select>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" color="amber">Filter</flux:button>
                    @if ($search !== '' || $selectedStatus !== '')
                        <flux:button href="{{ route('admin.customers.index') }}" variant="ghost">Reset</flux:button>
                    @endif
                </div>
            </form>
        </x-ui.filter-bar>

        @if ($customers->isEmpty())
            <x-ui.empty-state
                title="Belum ada customer sesuai filter"
                description="Ubah keyword atau status untuk melihat data yang lain."
                icon="users"
            />
        @else
            <x-ui.section-card title="Daftar customer" description="Pola list ini bisa dipakai ulang untuk modul products, orders, payments, dan reports.">
                <flux:table :paginate="$customers" container:class="overflow-x-auto">
                    <flux:table.columns>
                        <flux:table.column>Customer</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Catatan</flux:table.column>
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($customers as $customer)
                            <flux:table.row :key="$customer->id">
                                <flux:table.cell>
                                    <div class="min-w-64">
                                        <flux:heading>{{ $customer->user->name }}</flux:heading>
                                        <flux:text class="mt-1">{{ $customer->business_name ?: 'Usaha belum diisi' }}</flux:text>
                                        <flux:text class="mt-1">{{ $customer->user->email }} • {{ $customer->user->phone }}</flux:text>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="py-0">
                                    <x-ui.status-badge :status="$customer->verification_status" />
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:text>
                                        {{ $customer->rejection_reason ?: 'Belum ada catatan review.' }}
                                    </flux:text>
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="min-w-80 space-y-3">
                                        <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-3">
                                            @csrf
                                            @method('PATCH')

                                            <flux:textarea
                                                name="reason"
                                                rows="2"
                                                placeholder="Alasan penolakan / penangguhan"
                                            ></flux:textarea>

                                            <div class="flex flex-wrap justify-end gap-2">
                                                <flux:button href="{{ route('admin.customers.show', $customer) }}" variant="ghost" size="sm">
                                                    Detail
                                                </flux:button>
                                                <flux:button type="submit" name="action" value="approve" variant="primary" color="amber" size="sm">
                                                    Setujui
                                                </flux:button>
                                                <flux:button type="submit" name="action" value="reactivate" variant="filled" size="sm">
                                                    Aktifkan
                                                </flux:button>
                                                <flux:button type="submit" name="action" value="reject" variant="danger" size="sm">
                                                    Tolak
                                                </flux:button>
                                                <flux:button type="submit" name="action" value="suspend" variant="subtle" size="sm">
                                                    Tangguhkan
                                                </flux:button>
                                            </div>
                                        </form>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </x-ui.section-card>
        @endif
    </x-layout.admin-page>
</x-layouts.app>
