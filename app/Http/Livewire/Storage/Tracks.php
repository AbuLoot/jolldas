<?php

namespace App\Http\Livewire\Storage;

use Livewire\Component;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class Tracks extends Component
{
    public $lang;
    public $search;
    // public $tracks;

    protected $listeners = [
        'newData' => '$refresh',
    ];

    public function mount()
    {
        $this->lang = app()->getLocale();
    }

    public function render()
    {
        $tracks = Track::orderByDesc('id')
            ->when((strlen($this->search) >= 5), function($query) {
                $query->where('code', 'like', '%'.$this->search.'%');
            })
            ->paginate(50);

        return view('livewire.storage.tracks', ['tracks' => $tracks])
            ->layout('livewire.storage.layout');
    }
}
