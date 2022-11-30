<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class Arrival extends Component
{
    public $lang;
    public $search;
    public $status;
    public $trackCodes = [];
    public $tracksGroup = [];

    protected $listeners = [
        'newData' => '$refresh',
    ];

    public function mount()
    {
        if (! Gate::allows('arrival', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->status = Status::select('id', 'slug')
            ->where('slug', 'sent')
            ->orWhere('id', 4)
            ->first();
    }

    public function getTrackCodes($trackIds = [])
    {
        $trackIds = rtrim($trackIds, ']');
        $trackIds = ltrim($trackIds, '[');
        $ids = explode(',', $trackIds);

        $this->trackCodes = Track::whereIn('id', $ids)->get();
    }

    public function toAccept($trackIds)
    {
        $trackIds = rtrim($trackIds, ']');
        $trackIds = ltrim($trackIds, '[');
        $ids = explode(',', $trackIds);

        $status = Status::where('slug', 'arrived')
            ->orWhere('id', 5)
            ->select('id', 'slug')
            ->first();

        $tracks = $this->tracksGroup->whereIn('id', $ids);

        // Creating Track Status
        $tracksStatus = [];

        foreach($tracks as $track) {
            $tracksStatus[] = [
                'track_id' => $track->id, 'status_id' => $status->id, 'created_at' => now(), 'updated_at' => now(),
            ];
        }

        TrackStatus::insert($tracksStatus);

        // Updating Track Status
        Track::whereIn('id', $ids)->update(['status' => $status->id]);
    }

    public function render()
    {
        // $tracks = Track::query()
        //     ->whereHas('statuses', function($query) {
        //         return $query->where('slug', 'sent');
        //     })
        //     ->paginate(30);

        $this->tracksGroup = Track::where('status', $this->status->id)->get();

        return view('livewire.storage.arrival')
            ->layout('livewire.storage.layout');
    }
}
