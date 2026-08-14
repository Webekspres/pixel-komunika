<?php

namespace App\Livewire\Admin;

use App\Domains\Catalog\MediaLibraryService;
use App\Models\MediaLibrary as MediaLibraryModel;
use DomainException;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Media Library - Pixel Komunika')]
class MediaLibrary extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public $uploadFile = null;

    public ?int $previewId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedUploadFile(): void
    {
        $this->validate([
            'uploadFile' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $this->upload(app(MediaLibraryService::class));
    }

    public function upload(MediaLibraryService $service): void
    {
        $this->validate([
            'uploadFile' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $media = $service->upload($this->uploadFile);
        $this->uploadFile = null;

        session()->flash('status', "Media {$media->original_name} berhasil diunggah.");
    }

    public function delete(MediaLibraryService $service, int $mediaId): void
    {
        $media = MediaLibraryModel::query()->findOrFail($mediaId);

        try {
            $service->deleteLibraryItem($media);
            session()->flash('status', "Media {$media->original_name} berhasil dihapus.");
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $items = MediaLibraryModel::query()
            ->withCount('productUsages')
            ->when($this->search !== '', fn ($q) => $q->where('original_name', 'like', '%'.$this->search.'%'))
            ->orderByDesc('id')
            ->paginate(24);

        return view('livewire.admin.media-library', [
            'items' => $items,
        ])->layout('components.layouts.app');
    }
}
