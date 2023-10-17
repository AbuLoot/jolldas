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
    public $mode = 'list';
    public $trackCode;
    public $trackCodes = [];
    public $allReceivedTracks = [];

    protected $rules = [
        'trackCode' => 'required|string|min:10|max:20',
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

    public function getTrackCodesById($trackIds = [])
    {
        $trackIds = rtrim($trackIds, ']');
        $trackIds = ltrim($trackIds, '[');
        $ids = explode(',', $trackIds);

        $this->trackCodes = Track::whereIn('id', $ids)->get();
        $this->dispatchBrowserEvent('open-modal');
    }

    public function getTracksIdByDate($dateFrom, $dateTo)
    {
        $tracksGroup = $this->allReceivedTracks;

        $tracks = $tracksGroup->when($dateTo, function ($tracksGroup) use ($dateFrom, $dateTo) {

                // If tracks added today
                if ($dateTo == now()->format('Y-m-d H-i')) {
                    return $tracksGroup->where('updated_at', '>', $dateFrom.' 23:59:59')->where('updated_at', '<=', now());
                }

                return $tracksGroup->where('updated_at', '>', $dateFrom)->where('updated_at', '<', $dateTo);

            }, function ($tracksGroup) use ($dateFrom) {

                return $tracksGroup->where('updated_at', '<', $dateFrom);
            });

        return $tracks->pluck('id')->toArray();
    }

    public function openGroupByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $this->trackCodes = $this->allReceivedTracks->whereIn('id', $ids)->sortByDesc('id');

        $this->dispatchBrowserEvent('open-modal');
    }

    public function sendGroupByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $tracks = $this->allReceivedTracks->whereIn('id', $ids);

        $statusSent = Status::where('slug', 'sent')
            ->orWhere('id', 3)
            ->select('id', 'slug')
            ->first();

        // Creating Track Status
        $tracksStatus = [];

        $tracks->each(function ($track) use (&$tracksStatus, $statusSent) {
            $tracksStatus[] = [
                'track_id' => $track->id,
                'status_id' => $statusSent->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        TrackStatus::insert($tracksStatus);

        // Updating Track Status
        Track::whereIn('id', $ids)->update(['status' => $statusSent->id]);
    }

    public function btnToSend($trackCode)
    {
        $this->trackCode = $trackCode;
        $this->toSend();
        $this->search = null;
    }

    public function toSend()
    {
        $this->validate();

        $statusSent = Status::select('id', 'slug')
            ->where('slug', 'sent')
            ->orWhere('id', 3)
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

        if ($track->status >= $statusSent->id) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' sent');
            $this->trackCode = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $statusSent->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $statusSent->id;
        $track->save();

        $this->trackCode = null;
        $this->dispatchBrowserEvent('area-focus');
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function render()
    {
        if ($this->mode == 'list') {
            $receivedTracks = Track::query()->where('status', $this->status->id)->orderByDesc('id')->paginate(50);
        } else {
            $receivedTracks = Track::query()->where('status', $this->status->id)->get();
            $this->allReceivedTracks = $receivedTracks;
        }

        $tracks = [];

        if (strlen($this->search) >= 4) {
            $tracks = Track::query()
                ->orderByDesc('id')
                ->where('status', $this->status->id)
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        return view('livewire.storage.sending', [
                'tracks' => $tracks,
                'receivedTracks' => $receivedTracks,
            ])
            ->layout('livewire.storage.layout');
    }
}
