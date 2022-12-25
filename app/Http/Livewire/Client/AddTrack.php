<?php

namespace App\Http\Livewire\Client;

use Livewire\Component;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class AddTrack extends Component
{
    public $lang;
    public $search;
    public Track $track;

    protected $rules = [
        'track.code' => 'required|string|min:12|max:20',
        'track.description' => 'required|string|max:1000',
    ];

    public function mount()
    {
        $this->track = new Track();
        $this->lang = app()->getLocale();
    }

    public function addTrack()
    {
        $this->validate();

        $existsTrack = Track::where('code', $this->track->code)->first();

        if ($existsTrack && $existsTrack->user_id == null) {
            $existsTrack->user_id = auth()->user()->id;
            $existsTrack->description = $this->track->description;
            $existsTrack->save();

            $this->track->code = null;
            $this->track->description = null;

            $this->emitUp('newData');
            $this->dispatchBrowserEvent('show-toast', [
                'message' => 'Data added', 'selector' => 'closeAddTrack'
            ]);
            return;
        }

        if ($existsTrack) {
            $this->addError('track.code', 'Track code exists');
            return;
        }

        $status = Status::select('id', 'slug')
            ->where('slug', 'added')
            ->orWhere('id', 1)
            ->first();

        $this->track->user_id = auth()->user()->id;
        $this->track->lang = app()->getLocale();
        $this->track->status = $status->id;
        $this->track->save();

        if ($this->track->statuses()->where('id', $status->id)->first()) {
            $this->addError('track.code', 'Status added');
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $this->track->id;
        $trackStatus->status_id = $status->id;
        $trackStatus->save();

        $this->track->code = null;
        $this->track->description = null;

        $this->emitUp('newData');
        $this->dispatchBrowserEvent('show-toast', [
            'message' => 'Data added', 'selector' => 'closeAddTrack'
        ]);
    }

    public function render()
    {
        return view('livewire.client.add-track');
    }
}
