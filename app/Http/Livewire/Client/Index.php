<?php

namespace App\Http\Livewire\Client;

use Livewire\Component;

use App\Models\Track;

class Index extends Component
{
    public $lang;
    public $search;
    public Track $track;

    protected $listeners = [
        'newData' => '$refresh',
    ];

    public function mount()
    {
        $this->lang = app()->getLocale();
    }

    public function editTrack($id)
    {
        $this->emit('editTrack', $id);
    }

    public function deleteTrack($id)
    {
        Track::destroy($id);
    }

    public function render()
    {
        $tracks = Track::orderBy('id', 'desc')
            ->where('user_id', auth()->user()->id)
            ->when((strlen($this->search) >= 2), function($query) {
                $query->where('code', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->paginate(50);

        return view('livewire.client.index', ['tracks' => $tracks])
            ->layout('livewire.client.layout');
    }
}
