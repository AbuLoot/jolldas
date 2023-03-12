<?php

namespace App\Http\Livewire\Storage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;

use Rap2hpoutre\FastExcel\FastExcel;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;

class Reception extends Component
{
    use WithFileUploads;

    public $lang;
    public $search;
    public $trackCode;
    public $tracksDoc;

    protected $rules = [
        'trackCode' => 'required|string|min:10|max:20',
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

    public function toReceive()
    {
        $this->validate();

        $statusReceived = Status::select('id', 'slug')
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

        if ($track->status >= $statusReceived->id) {
            $this->addError('trackCode', 'Track '.$this->trackCode.' received');
            $this->trackCode = null;
            return;
        }

        $trackStatus = new TrackStatus();
        $trackStatus->track_id = $track->id;
        $trackStatus->status_id = $statusReceived->id;
        $trackStatus->created_at = now();
        $trackStatus->updated_at = now();
        $trackStatus->save();

        $track->status = $statusReceived->id;
        $track->save();

        $this->dispatchBrowserEvent('area-focus');
    }

    public function uploadDoc(Request $request)
    {
        $this->validate([
            'tracksDoc' => 'required|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);

        // dd($request->file('tracksDoc'));

        $tracksDoc = (new FastExcel)->import('tracksDoc', function($line) {
            dd($line);
            // return = [
                // 'code' => 
            // ];
        });
    }

    public function render()
    {
        $tracks = Track::query()
            ->orderByDesc('id')
            ->where('status', 2)
            ->when((strlen($this->search) >= 4), function($query) {
                $query->where('code', 'like', '%'.$this->search.'%');
            })
            ->paginate(50);

        return view('livewire.storage.reception', ['tracks' => $tracks])
            ->layout('livewire.storage.layout');
    }
}
