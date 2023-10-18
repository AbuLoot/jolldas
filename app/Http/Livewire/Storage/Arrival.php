<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

use App\Models\User;
use App\Models\Region;
use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

use App\Jobs\SendMailNotification;

class Arrival extends Component
{
    public $lang;
    public $mode = 'list';
    public $search;
    public $status;
    public $region;
    public $idClient = 'J7799';
    public $trackCode;
    public $trackCodes = [];
    public $allSentLocallyTracks = [];
    public $text;

    public function mount()
    {
        if (! Gate::allows('arrival', auth()->user())) {
            abort(403);
        }

        $this->lang = app()->getLocale();
        $this->status = Status::select('id', 'slug')
            ->where('slug', 'sent-locally')
            ->orWhere('id', 7)
            ->first();

        if (!session()->has('jRegion')) {
            $region = auth()->user()->region()->first() ?? Region::where('slug', 'kazakhstan')->orWhere('id', 1)->first();
            session()->put('jRegion', $region);
        }
    }

    public function getTracksIdByDate($dateFrom, $dateTo)
    {
        $sentLocallyTracks = $this->allSentLocallyTracks;

        $tracks = $sentLocallyTracks->when($dateTo, function ($sentLocallyTracks) use ($dateFrom, $dateTo) {

                // If tracks added today
                if ($dateTo == now()->format('Y-m-d H-i')) {
                    return $sentLocallyTracks->where('updated_at', '>', $dateFrom.' 23:59:59')->where('updated_at', '<=', now());
                }

                return $sentLocallyTracks->where('updated_at', '>', $dateFrom)->where('updated_at', '<', $dateTo);

            }, function ($sentLocallyTracks) use ($dateFrom) {

                return $sentLocallyTracks->where('updated_at', '<', $dateFrom);
            });

        return $tracks->pluck('id')->toArray();
    }

    public function openGroupByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $this->trackCodes = $this->allSentLocallyTracks->whereIn('id', $ids)->sortByDesc('id');

        $this->dispatchBrowserEvent('open-modal');
    }

    public function groupArrivedByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $tracks = $this->allSentLocallyTracks->whereIn('id', $ids);

        $statusArrived = Status::where('slug', 'arrived')
            ->orWhere('id', 5)
            ->select('id', 'slug')
            ->first();

        // Creating Track Status
        $tracksStatus = [];
        $tracksUsers = [];

        foreach($tracks as $track) {
            $tracksStatus[] = [
                'track_id' => $track->id, 'status_id' => $statusArrived->id, 'created_at' => now(), 'updated_at' => now(),
            ];

            if (isset($track->user->email) && !in_array($track->user->email, $tracksUsers)) {
                $tracksUsers[] = $track->user->email;
            }
        }

        TrackStatus::insert($tracksStatus);

        // Updating Track Status
        Track::whereIn('id', $ids)->update(['status' => $statusArrived->id]);

        SendMailNotification::dispatch($tracksUsers);
    }

    public function btnToArrive($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->toArrive();
        // $this->search = null;
    }

    public function toArrive()
    {
        $this->validate(['trackCode' => 'required|string|min:10|max:20']);

        $statusArrived = Status::select('id', 'slug')
            ->where('slug', 'arrived')
            ->orWhere('id', 5)
            ->first();

        $track = Track::where('code', $this->trackCode)->first();

        if (!$track) {
            $newTrack = new Track;
            $newTrack->user_id = session('arrivalToUser')->id ?? null;
            $newTrack->lang = $this->lang;
            $newTrack->code = $this->trackCode;
            $newTrack->description = '';
            $newTrack->text = $this->text;
            $newTrack->save();

            $this->text = null;
            $track = $newTrack;
        }

        if ($track->status >= $statusArrived->id AND $track->status != 7) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' arrived');
            $this->trackCode = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $statusArrived->id;
        $trackStatus->region_id = $this->region->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->user_id = session('arrivalToUser')->id ?? $track->user_id;
        $track->status = $statusArrived->id;
        $track->save();

        if (isset($track->user->email)) {
            SendMailNotification::dispatch($track->user->email);
        }

        $this->trackCode = null;
        $this->dispatchBrowserEvent('area-focus');
    }

    public function attachUser($id)
    {
        session()->put('arrivalToUser', User::findOrFail($id));
    }

    public function detachUser()
    {
        session()->forget('arrivalToUser');
        $this->idClient = 'J7799';
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
            $sentLocallyTracks = Track::query()->where('status', $this->status->id)->orderByDesc('id')->paginate(50);
        } else {
            $sentLocallyTracks = Track::query()->where('status', $this->status->id)->orderByDesc('id')->get();
            $this->allSentLocallyTracks = $sentLocallyTracks;
        }

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

        return view('livewire.storage.arrival', [
                'tracks' => $tracks,
                'users' => $users,
                'sentLocallyTracks' => $sentLocallyTracks,
                'regions' => Region::descendantsAndSelf(1)->toTree(),
            ])
            ->layout('livewire.storage.layout');
    }
}
