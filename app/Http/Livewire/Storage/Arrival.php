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
    public $trackCode;
    public $trackCodes = [];
    public $tracksGroup = [];

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
    }

    public function getTracksIdByDate($dateFrom, $dateTo)
    {
        $tracksGroup = $this->tracksGroup;

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

        $this->trackCodes = $this->tracksGroup->whereIn('id', $ids)->sortByDesc('id');

        $this->dispatchBrowserEvent('open-modal');
    }

    public function groupArrivedByDate($dateFrom, $dateTo)
    {
        $ids = $this->getTracksIdByDate($dateFrom, $dateTo);

        $tracks = $this->tracksGroup->whereIn('id', $ids);

        $statusArrived = Status::where('slug', 'arrived')
            ->orWhere('id', 5)
            ->select('id', 'slug')
            ->first();

        // Creating Track Status
        $tracksStatus = [];

        foreach($tracks as $track) {
            $tracksStatus[] = [
                'track_id' => $track->id, 'status_id' => $statusArrived->id, 'created_at' => now(), 'updated_at' => now(),
            ];
        }

        TrackStatus::insert($tracksStatus);

        // Updating Track Status
        Track::whereIn('id', $ids)->update(['status' => $statusArrived->id]);
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
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $statusArrived->id;
        $track->save();

        $this->trackCode = null;
        $this->dispatchBrowserEvent('area-focus');
    }

    public function render()
    {
        // $tracks = Track::query()
        //     ->whereHas('statuses', function($query) {
        //         return $query->where('slug', 'sent');
        //     })
        //     ->paginate(30);

        $this->tracksGroup = Track::where('status', $this->status->id)->get();

        $tracks = [];

        if (strlen($this->search) >= 4) {
            $tracks = Track::query()
                ->orderByDesc('id')
                ->where('status', $this->status->id)
                ->where('code', 'like', '%'.$this->search.'%')
                ->paginate(10);
        }

        return view('livewire.storage.arrival', ['tracks' => $tracks])
            ->layout('livewire.storage.layout');
    }
}
