<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Admin - Audit Log')]
class AdminAuditLog extends Component
{
    use WithPagination;

    #[Url]
    public string $action = '';

    #[Url]
    public string $actorId = '';

    #[Url]
    public string $auditableType = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function updatingAction(): void
    {
        $this->resetPage();
    }

    public function updatingActorId(): void
    {
        $this->resetPage();
    }

    public function updatingAuditableType(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['action', 'actorId', 'auditableType', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::query()->with('actor')->orderByDesc('id');

        if ($this->action !== '') {
            $query->where('action', $this->action);
        }

        if ($this->actorId !== '') {
            $query->where('actor_user_id', (int) $this->actorId);
        }

        if ($this->auditableType !== '') {
            $query->where('auditable_type', $this->auditableType);
        }

        if ($this->dateFrom !== '') {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo !== '') {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');
        $types = AuditLog::query()->distinct()->orderBy('auditable_type')->pluck('auditable_type');
        $actors = User::query()
            ->whereIn('id', AuditLog::query()->whereNotNull('actor_user_id')->distinct()->pluck('actor_user_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.admin.admin-audit-log', [
            'logs' => $query->paginate(20),
            'actions' => $actions,
            'types' => $types,
            'actors' => $actors,
        ])->layout('components.layouts.app');
    }
}
