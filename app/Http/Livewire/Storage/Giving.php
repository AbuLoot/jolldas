<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

use App\Models\User;
use App\Models\Region;
use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class Giving extends Component
{
    public $lang;
    public $search;
    public $status;
    public $region;
    public $idClient = 'J7799';
    public $trackCode;
    public $text;

    protected $rules = [
        'trackCode' => 'required|string|min:10|max:20',
    ];

    public function mount()
    {
        if (! Gate::allows('giving', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->status = Status::select('id', 'slug')
            ->where('slug', 'given')
            ->orWhere('id', 7)
            ->first();

        if (!session()->has('jRegion')) {
            $region = auth()->user()->region()->first() ?? Region::where('slug', 'kazakhstan')->orWhere('id', 1)->first();
            session()->put('jRegion', $region);
        }
    }

    public function btnToGive($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->toGive();
        $this->search = null;
    }

    public function toGive()
    {
        $this->validate();

        $statusGiven = Status::select('id', 'slug')
            ->where('slug', 'giving')
            ->orWhere('id', 6)
            ->first();

        $track = Track::where('code', $this->trackCode)->first();

        if (!$track) {
            $newTrack = new Track;
            $newTrack->user_id = session('givingToUser')->id ?? null;
            $newTrack->lang = $this->lang;
            $newTrack->code = $this->trackCode;
            $newTrack->description = '';
            $newTrack->text = $this->text;
            $newTrack->save();

            $track = $newTrack;
        }

        if ($track->status >= $statusGiven->id AND $track->status != 7) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' given');
            $this->trackCode = null;
            $this->text = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $statusGiven->id;
        $trackStatus->region_id = $this->region->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->user_id = session('givingToUser')->id ?? $track->user_id;
        $track->status = $statusGiven->id;
        $track->text = $this->text;
        $track->save();

        $this->trackCode = null;
        $this->text = null;
        $this->dispatchBrowserEvent('area-focus');
    }

    public function attachUser($id)
    {
        session()->put('givingToUser', User::findOrFail($id));
    }

    public function detachUser()
    {
        session()->forget('givingToUser');
        $this->idClient = 'J7799';
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
        $arrivedTracks = Track::query()->where('status', $this->status->id)->orderByDesc('id')->paginate(50);

        $this->region = session()->get('jRegion');
        $this->setRegionId = session()->get('jRegion')->id;

        $tracks = [];
        $users = [];

        if (strlen($this->search) >= 4) {
            $tracks = Track::query()
                ->orderByDesc('id')
                ->where('status', $this->status->id)
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        if (strlen($this->idClient) >= 9) {
            $users = User::orderBy('id', 'desc')
                ->where('id_client', 'like', '%'.$this->idClient.'%')
                ->get()
                ->take(10);
        }

        return view('livewire.storage.giving', [
                'tracks' => $tracks,
                'users' => $users,
                'arrivedTracks' => $arrivedTracks,
                'regions' => Region::descendantsAndSelf(1)->toTree(),
            ])->layout('livewire.storage.layout');
    }
}
