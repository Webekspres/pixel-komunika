<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pixel Komunika')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.pages.home')
            ->layout('layouts.guest');
    }
}
