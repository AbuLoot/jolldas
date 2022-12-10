<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class Reception extends Component
{
    public $lang;
    public $search;
    public $trackCode;

    protected $rules = [
        'trackCode' => 'required|string|min:12|max:20',
    ];

    protected $listeners = [
        'newData' => '$refresh',
    ];

    public function mount()
    {
        if (auth()->user()->roles->first()->name == 'storekeeper-last') {
            return redirect($this->lang.'/storage/arrival');
        }

        if (! Gate::allows('reception', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
    }

    public function toAccept()
    {
        $this->validate();

        $status = Status::select('id', 'slug')
            ->where('slug', 'received')
            ->orWhere('id', 2)
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

        if ($track->status == $status->id) {
            $this->addError('trackCode', 'Track received');
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $status->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $status->id;
        $track->save();

        $this->dispatchBrowserEvent('area-focus');
    }

    public function render()
    {
        $tracks = Track::query()
            ->orderByDesc('id')
            ->where('status', 2)
            ->when((strlen($this->search) >= 5), function($query) {
                $query->where('code', 'like', '%'.$this->search.'%');
            })
            ->paginate(50);

        return view('livewire.storage.reception', ['tracks' => $tracks])
            ->layout('livewire.storage.layout');
    }
}
