<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Rap2hpoutre\FastExcel\FastExcel;

use App\Models\Region;
use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class SendLocally extends Component
{
    public $lang;
    public $search;
    public $status;
    public $region;
    public $trackCode;

    public function mount()
    {
        if (! Gate::allows('sending-locally', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->status = Status::select('id', 'slug')
            ->where('slug', 'sent-locally')
            ->orWhere('id', 5)
            ->first();

        if (!session()->has('jRegion')) {
            $region = auth()->user()->region()->first() ?? Region::where('slug', 'kazakhstan')->orWhere('id', 1)->first();
            session()->put('jRegion', $region);
        }
    }

    public function btnToSendLocally($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->toSendLocally();
    }

    public function toSendLocally()
    {
        $this->validate(['trackCode' => 'required|string|min:10|max:20']);

        $statusSentLocally = Status::select('id', 'slug')
            ->where('slug', 'sent-locally')
            ->orWhere('id', 5)
            ->first();

        $track = Track::where('code', $this->trackCode)->first();

        if (!$track) {
            $newTrack = new Track;
            $newTrack->lang = $this->lang;
            $newTrack->code = $this->trackCode;
            $newTrack->description = '';
            $newTrack->save();

            $track = $newTrack;
        }

        if ($track->status >= $statusSentLocally->id) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' sent locally');
            $this->trackCode = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $statusSentLocally->id;
        $trackStatus->region_id = $this->region->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $statusSentLocally->id;
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

        $sentLocallyTracks = Track::query()->where('status', $this->status->id)->orderByDesc('updated_at')->paginate(50);

        $tracks = [];

        if (strlen($this->search) >= 4) {
            $tracks = Track::query()
                ->orderByDesc('updated_at')
                ->where('status', $this->status->id)
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        return view('livewire.storage.send-locally', [
                'tracks' => $tracks,
                'sentLocallyTracks' => $sentLocallyTracks,
                'regions' => Region::descendantsAndSelf(1)->toTree(),
            ])
            ->layout('livewire.storage.layout');
    }
}
