<div class="space-y-5 p-6">
    <div>
        <h1 class="text-xl font-black text-zinc-900">Audit Log</h1>
        <p class="mt-0.5 text-sm text-zinc-500">Jejak tindakan administratif kritis (FR-AUD-002).</p>
    </div>

    <div class="rounded-2xl border border-neutral-100 bg-white">
        <div class="flex flex-col gap-3 border-b border-neutral-100 px-4 py-4 lg:flex-row lg:flex-wrap lg:items-end">
            <div class="min-w-[10rem]">
                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-zinc-500">Aksi</label>
                <select wire:model.live="action" class="w-full rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-sm">
                    <option value="">Semua aksi</option>
                    @foreach ($actions as $actionOption)
                        <option value="{{ $actionOption }}">{{ $actionOption }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[10rem]">
                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-zinc-500">Aktor</label>
                <select wire:model.live="actorId" class="w-full rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-sm">
                    <option value="">Semua aktor</option>
                    @foreach ($actors as $actor)
                        <option value="{{ $actor->id }}">{{ $actor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[12rem]">
                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-zinc-500">Entity</label>
                <select wire:model.live="auditableType" class="w-full rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-sm">
                    <option value="">Semua entity</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}">{{ class_basename($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-zinc-500">Dari</label>
                <input type="date" wire:model.live="dateFrom" class="rounded-xl border border-neutral-200 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-zinc-500">Sampai</label>
                <input type="date" wire:model.live="dateTo" class="rounded-xl border border-neutral-200 px-3 py-2.5 text-sm">
            </div>
            <button type="button" wire:click="clearFilters" class="rounded-xl border border-neutral-200 px-3.5 py-2.5 text-xs font-bold text-zinc-600 hover:bg-neutral-50">
                Reset
            </button>
        </div>

        @if ($logs->isEmpty())
            <div class="px-5 py-12">
                <x-ui.empty-state
                    title="Tidak Ada Audit Log"
                    description="Belum ada jejak yang cocok dengan filter."
                    icon="scroll-text"
                />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-neutral-100 bg-zinc-50/80 text-[10px] font-bold uppercase tracking-wider text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Aktor</th>
                            <th class="px-4 py-3">Aksi</th>
                            <th class="px-4 py-3">Entity</th>
                            <th class="px-4 py-3">Request ID</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-zinc-50/50">
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-zinc-600">
                                    {{ $log->created_at?->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-xs font-semibold text-zinc-900">
                                    {{ $log->actor?->name ?? 'Sistem' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-lg bg-zinc-100 px-2 py-0.5 font-mono text-[11px] font-bold text-zinc-800">{{ $log->action }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-zinc-600">
                                    {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                </td>
                                <td class="px-4 py-3 font-mono text-[10px] text-zinc-400">
                                    {{ $log->request_id ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-neutral-100 px-4 py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
