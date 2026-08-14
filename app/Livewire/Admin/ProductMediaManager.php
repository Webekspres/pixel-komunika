<?php

namespace App\Livewire\Admin;

use App\Domains\Catalog\MediaLibraryService;
use App\Models\MediaLibrary;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductMediaManager extends Component
{
    use WithFileUploads;

    public Product $product;

    public bool $pickerOpen = false;

    public string $search = '';

    /** @var array<int> */
    public array $selected = [];

    public $uploadFile = null;

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function updatedUploadFile(): void
    {
        $this->validate([
            'uploadFile' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $this->uploadNew(app(MediaLibraryService::class));
    }

    public function uploadNew(MediaLibraryService $service): void
    {
        $this->validate([
            'uploadFile' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $media = $service->upload($this->uploadFile);
        $this->uploadFile = null;
        $service->attachToProduct($this->product, $media);

        $this->dispatch('media-uploaded', name: $media->original_name);
    }

    public function closePicker(): void
    {
        $this->selected = [];
        $this->search = '';
        $this->pickerOpen = false;
    }

    public function attachSelected(MediaLibraryService $service): void
    {
        $count = 0;

        foreach ($this->selected as $mediaId) {
            $media = MediaLibrary::query()->find($mediaId);

            if ($media) {
                $service->attachToProduct($this->product, $media);
                $count++;
            }
        }

        $this->closePicker();

        if ($count > 0) {
            $this->dispatch('media-attached', count: $count);
        }
    }

    public function setPrimary(int $usageId): void
    {
        $this->product->media()->whereKeyNot($usageId)->update(['is_primary' => false]);
        $this->product->media()->findOrFail($usageId)->update(['is_primary' => true]);
    }

    public function moveUp(int $usageId): void
    {
        $this->move($usageId, -1);
    }

    public function moveDown(int $usageId): void
    {
        $this->move($usageId, 1);
    }

    protected function move(int $usageId, int $direction): void
    {
        $usages = $this->product->media()->orderBy('sort_order')->get();
        $index = $usages->search(fn ($usage) => $usage->id === $usageId);
        $swap = $usages[$index + $direction] ?? null;

        if ($swap !== null) {
            $current = $usages[$index];
            $tmp = $current->sort_order;
            $current->update(['sort_order' => $swap->sort_order]);
            $swap->update(['sort_order' => $tmp]);
        }
    }

    public function updateAltText(int $usageId, string $altText): void
    {
        $this->product->media()->findOrFail($usageId)->update(['alt_text' => $altText]);
    }

    public function detach(int $usageId, MediaLibraryService $service): void
    {
        $usage = $this->product->media()->findOrFail($usageId);
        $service->detachFromProduct($usage);
    }

    public function render()
    {
        $usages = $this->product->media()->with('library')->orderBy('sort_order')->get();

        $library = MediaLibrary::query()
            ->withCount('productUsages')
            ->when($this->search !== '', fn ($q) => $q->where('original_name', 'like', '%'.$this->search.'%'))
            ->orderByDesc('id')
            ->limit(48)
            ->get();

        $attachedIds = $usages->map(fn ($usage) => (int) $usage->media_id)->all();
        $selectedIds = array_map('intval', $this->selected);

        return view('livewire.admin.product-media-manager', [
            'usages' => $usages,
            'library' => $library,
            'attachedIds' => $attachedIds,
            'selectedIds' => $selectedIds,
        ]);
    }
}
