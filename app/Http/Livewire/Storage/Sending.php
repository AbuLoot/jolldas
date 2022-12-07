<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class Sending extends Component
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
        if (! Gate::allows('sending', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->status = Status::select('id', 'slug')
            ->where('slug', 'received')
            ->orWhere('id', 2)
            ->first();
    }

    public function getTrackCodes($trackIds = [])
    {
        $trackIds = rtrim($trackIds, ']');
        $trackIds = ltrim($trackIds, '[');
        $ids = explode(',', $trackIds);

        $this->trackCodes = Track::whereIn('id', $ids)->get();
        $this->dispatchBrowserEvent('open-modal');
    }

    public function toSend($trackIds = [])
    {
        $trackIds = rtrim($trackIds, ']');
        $trackIds = ltrim($trackIds, '[');
        $ids = explode(',', $trackIds);

        $status = Status::where('slug', 'sent')
            ->orWhere('id', 4)
            ->select('id', 'slug')
            ->first();

        $tracks = $this->tracksGroup->whereIn('id', $ids);

        // Creating Track Status
        $tracksStatus = [];
        $tracks->each(function ($track) use (&$tracksStatus, $status) {
            $tracksStatus[] = [
                'track_id' => $track->id,
                'status_id' => $status->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        TrackStatus::insert($tracksStatus);

        // Updating Track Status
        Track::whereIn('id', $ids)->update(['status' => $status->id]);
    }

    public function render()
    {
        $this->tracksGroup = Track::where('status', $this->status->id)->get();

        return view('livewire.storage.sending')
            ->layout('livewire.storage.layout');
    }
}
