<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

use App\Models\Region;
use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

use App\Jobs\SendMailNotification;

class Arrival extends Component
{
    public $lang;
    public $search;
    public $status;
    public $region;
    public $mode = 'list';
    public $trackCode;
    public $trackCodes = [];
    public $allSentTracks = [];

    protected $rules = [
        'trackCode' => 'required|string|min:10|max:20',
    ];

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

        if (!Cache::has('region')) {
            $region = Region::where('slug', 'kazakhstan')->orWhere('id', 1)->first();
            Cache::put('region', $region);
        }
    }

    public function getTracksIdByDate($dateFrom, $dateTo)
    {
        $sentTracks = $this->allSentTracks;

        $tracks = $sentTracks->when($dateTo, function ($sentTracks) use ($dateFrom, $dateTo) {

                // If tracks added today
                if ($dateTo == now()->format('Y-m-d H-i')) {
                    return $sentTracks->where('updated_at', '>', $dateFrom.' 23:59:59')->where('updated_at', '<=', now());
                }

                return $sentTracks->where('updated_at', '>', $dateFrom)->where('updated_at', '<', $dateTo);

            }, function ($sentTracks) use ($dateFrom) {

                return $sentTracks->where('updated_at', '<', $dateFrom);
            });

        return $tracks->pluck('id')->toArray();
    }

    public function openGroupByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $this->trackCodes = $this->allSentTracks->whereIn('id', $ids)->sortByDesc('id');

        $this->dispatchBrowserEvent('open-modal');
    }

    public function groupArrivedByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $tracks = $this->allSentTracks->whereIn('id', $ids);

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
        $this->search = null;
    }

    public function toArrive()
    {
        $this->validate();

        $statusArrived = Status::select('id', 'slug')
            ->where('slug', 'arrived')
            ->orWhere('id', 5)
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

        if ($track->status >= $statusArrived->id) {
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

        $track->status = $statusArrived->id;
        $track->save();

        if (isset($track->user->email)) {
            SendMailNotification::dispatch($track->user->email);
        }

        $this->trackCode = null;
        $this->dispatchBrowserEvent('area-focus');
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function setRegionId($id)
    {
        $region = Region::find($id);
        Cache::put('region', $region);
    }

    public function render()
    {
        if ($this->mode == 'list') {
            $sentTracks = Track::query()->where('status', $this->status->id)->orderByDesc('id')->paginate(50);
        } else {
            $sentTracks = Track::query()->where('status', $this->status->id)->orderByDesc('id')->get();
            $this->allSentTracks = $sentTracks;
        }

        $this->region = Cache::get('region');

        $tracks = [];

        if (strlen($this->search) >= 4) {
            $tracks = Track::query()
                ->orderByDesc('id')
                ->where('status', $this->status->id)
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        return view('livewire.storage.arrival', [
                'tracks' => $tracks,
                'sentTracks' => $sentTracks,
                'regions' => Region::descendantsAndSelf(1)->toTree(),
            ])
            ->layout('livewire.storage.layout');
    }
}
