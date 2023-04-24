<?php

namespace App\Http\Livewire\Client;

use Livewire\Component;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class EditTrack extends Component
{
    public $lang;
    public $search;
    public Track $track;

    protected $listeners = ['editTrack' => 'editTrack'];

    protected $rules = [
        'track.code' => 'required|string|min:10|max:20',
        'track.description' => 'required|string|max:1000',
    ];

    public function mount()
    {
        $this->lang = app()->getLocale();
    }

    public function editTrack($id)
    {
        $this->track = Track::find($id);

        $this->dispatchBrowserEvent('open-modal');
    }

    public function saveTrack()
    {
        $this->validate();

        $existsTrack = Track::where('code', $this->track->code)->where('id', '!=', $this->track->id)->first();

        if ($existsTrack) {
            $this->addError('track.code', 'Track code exists');
            return;
        }

        if ($this->track->status == 1 || $this->track->slug == 'added') {
            $this->track->save();
        } else {
            Track::where('id', $this->track->id)->update(['description' => $this->track->description]);
        }

        $this->track->code = null;
        $this->track->description = null;

        $this->emitUp('newData');
        $this->dispatchBrowserEvent('show-toast', [
            'message' => 'Data saved', 'selector' => 'closeEditTrack'
        ]);
    }

    public function render()
    {
        return view('livewire.client.edit-track');
    }
}
