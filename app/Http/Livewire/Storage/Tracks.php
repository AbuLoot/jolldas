<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;
use App\Models\Region;

class Tracks extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $lang;
    public $search;
    public $tracksStatus = 0;
    public $tracksRegion = 0;
    public $sort = 'desc';

    public function mount()
    {
        $this->lang = app()->getLocale();
    }

    public function resetFilter()
    {
        $this->tracksStatus = 0;
        $this->tracksRegion = 0;
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
        $regions = Region::get();

        $tracksStatus = $this->tracksStatus;
        $tracksRegion = $this->tracksRegion;

        $tracks = Track::orderBy('id', $this->sort)
            ->when((strlen($this->search) >= 4), function($query) {
                $query->where('code', 'like', '%'.$this->search.'%');
            })
            ->when($this->tracksStatus != 0, function($query) use ($tracksStatus) {
                $query->where('status', $tracksStatus);
            })
            ->when($this->tracksRegion != 0, function($query) use ($tracksRegion) {
                $query->whereHas('statuses', function(Builder $subQuery) use ($tracksRegion) {
                    $subQuery->where('region_id', $tracksRegion);
                });
            })
            ->paginate(50);

        $tracksCount = Track::when($this->tracksStatus != 0, function($query) use ($tracksStatus) {
                $query->where('status', $tracksStatus);
            })
            ->when($this->tracksRegion != 0, function($query) use ($tracksRegion) {
                $query->whereHas('statuses', function(Builder $subQuery) use ($tracksRegion) {
                    $subQuery->where('region_id', $tracksRegion);
                });
            })
            ->count();

        return view('livewire.storage.tracks', [
                'tracks' => $tracks,
                'tracksCount' => $tracksCount,
                'statuses' => $statuses,
                'regions' => $regions,
            ])
            ->layout('livewire.storage.layout');
    }
}
