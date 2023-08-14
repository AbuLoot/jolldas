<?php

namespace App\Http\Livewire\Storage;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class Tracks extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $lang;
    public $search;
    public $tracksStatus = 0;
    public $sort = 'desc';

    protected $listeners = [
        'newData' => '$refresh',
    ];

    public function mount()
    {
        $this->lang = app()->getLocale();
    }

    public function resetFilter()
    {
        $this->tracksStatus = 0;
        $this->sort = 'desc';
    }

    public function applyFilter()
    {
        // Don`t touch this function!

        $this->search = null;
        $this->resetPage();
    }

    public function render()
    {
        $statuses = Status::get();

        $tracksStatus = $this->tracksStatus;

        $tracksCount = Track::when($this->tracksStatus != 0, function($query) use ($tracksStatus) {
                $query->where('status', $tracksStatus);
            })
            ->count();

        $tracks = Track::orderBy('id', $this->sort)
            ->when((strlen($this->search) >= 4), function($query) {
                $query->where('code', 'like', '%'.$this->search.'%');
            })
            ->when($this->tracksStatus != 0, function($query) use ($tracksStatus) {
                $query->where('status', $tracksStatus);
            })
            ->paginate(50);

        return view('livewire.storage.tracks', [
                'tracks' => $tracks,
                'tracksCount' => $tracksCount,
                'statuses' => $statuses,
            ])
            ->layout('livewire.storage.layout');
    }
}
