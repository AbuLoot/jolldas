<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

use App\Models\Region;
use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class Sorting extends Component
{
    public $lang;
    public $search;
    public $region;
    public $mode = 'list';
    public $status = [];
    public $trackCode;
    public $trackCodes = [];
    public $allSortedTracks = [];

    protected $rules = [
        'trackCode' => 'required|string|min:10|max:20',
    ];

    public function mount()
    {
        if (! Gate::allows('sorting', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->status = Status::select('id', 'slug')
            ->where('slug', 'sorting')
            ->orWhere('id', 4)
            ->first();

        if (!session()->has('jRegion')) {
            $region = auth()->user()->region()->first() ?? Region::where('slug', 'kazakhstan')->orWhere('id', 1)->first();
            session()->put('jRegion', $region);
        }

        $this->region = session()->get('jRegion');
        $this->setRegionId = session()->get('jRegion')->id;
    }

    public function toSort()
    {
        $this->validate();

        $statusSorted = Status::select('id', 'slug')
            ->where('slug', 'sorted')
            ->orWhere('id', 4)
            ->first();

        $track = Track::where('code', $this->trackCode)->first();

        if (!$track) {
            $newTrack = new Track;
            $newTrack->user_id = null;
            $newTrack->lang = $this->lang;
            $newTrack->code = $this->trackCode;
            $newTrack->description = '';
            $newTrack->save();

            $track = $newTrack;
        }

        if ($track->status >= $statusSorted->id) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' sorted');
            $this->trackCode = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $statusSorted->id;
        $trackStatus->region_id = $this->region->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $statusSorted->id;
        $track->save();

        $this->trackCode = null;
        $this->dispatchBrowserEvent('area-focus');
    }

    public function setRegionId($id)
    {
        if (! Gate::allows('setting-regions', auth()->user())) {
            abort(403);
        }

        $region = Region::find($id);
        session()->put('jRegion', $region);
    }

    public function render()
    {
        $this->region = session()->get('jRegion');
        $this->setRegionId = session()->get('jRegion')->id;

        $sortedTracks = Track::query()->where('status', $this->status->id)->orderByDesc('updated_at')->paginate(50);

        $tracks = [];

        if (strlen($this->search) >= 4) {
            $tracks = Track::query()
                ->orderByDesc('updated_at')
                ->where('status', $this->status->id)
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        return view('livewire.storage.sorting', [
                'tracks' => $tracks,
                'sortedTracks' => $sortedTracks,
                'regions' => Region::descendantsAndSelf(1)->toTree(),
            ])
            ->layout('livewire.storage.layout');
    }
}
