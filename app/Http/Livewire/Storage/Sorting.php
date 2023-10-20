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
    }

    public function getTracksIdByDate($dateFrom, $dateTo)
    {
        $sortedTracks = $this->allSortedTracks;

        $tracks = $sortedTracks->when($dateTo, function ($sortedTracks) use ($dateFrom, $dateTo) {

                // If tracks added today
                if ($dateTo == now()->format('Y-m-d H-i')) {
                    return $sortedTracks->where('updated_at', '>', $dateFrom.' 23:59:59')->where('updated_at', '<=', now());
                }

                return $sortedTracks->where('updated_at', '>', $dateFrom)->where('updated_at', '<', $dateTo);

            }, function ($sortedTracks) use ($dateFrom) {

                return $sortedTracks->where('updated_at', '<', $dateFrom);
            });

        return $tracks->pluck('id')->toArray();
    }

    public function openGroupByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $this->trackCodes = $this->allSortedTracks->whereIn('id', $ids)->sortByDesc('id');

        $this->dispatchBrowserEvent('open-modal');
    }

    public function groupSortedByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $tracks = $this->allSortedTracks->whereIn('id', $ids);

        $statusSorted = Status::where('slug', 'sorted')
            ->orWhere('id', 4)
            ->select('id', 'slug')
            ->first();

        // Creating Track Status
        $tracksStatus = [];
        $tracksUsers = [];

        foreach($tracks as $track) {
            $tracksStatus[] = [
                'track_id' => $track->id, 'status_id' => $statusSorted->id, 'created_at' => now(), 'updated_at' => now(),
            ];

            if (isset($track->user->email) && !in_array($track->user->email, $tracksUsers)) {
                $tracksUsers[] = $track->user->email;
            }
        }

        TrackStatus::insert($tracksStatus);

        // Updating Track Status
        Track::whereIn('id', $ids)->update(['status' => $statusSorted->id]);
    }

    public function btnToSort($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->toSort();
        // $this->search = null;
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

    public function setMode($mode)
    {
        $this->mode = $mode;
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
        if ($this->mode == 'list') {
            $sortedTracks = Track::query()->where('status', $this->status->id)->orderByDesc('updated_at')->paginate(50);
        } else {
            $sortedTracks = Track::query()->where('status', $this->status->id)->orderByDesc('updated_at')->get();
            $this->allSortedTracks = $sortedTracks;
        }

        $this->region = session()->get('jRegion');
        $this->setRegionId = session()->get('jRegion')->id;

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
