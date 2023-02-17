<?php

namespace App\Http\Livewire\Client;

use Livewire\Component;

use App\Models\Track;

class Archive extends Component
{
    public $lang;
    public $search;

    protected $listeners = [
        'newData' => '$refresh',
    ];

    public function mount()
    {
        $this->lang = app()->getLocale();
    }

    public function toggleTrack($id)
    {
        Track::where('id', $id)->update(['state' => 1]);
    }

    public function render()
    {
        $tracks = Track::where('user_id', auth()->user()->id)
            ->where('state', 0)
            ->orderBy('id', 'desc')
            ->when((strlen($this->search) >= 2), function($query) {
                $query->where('code', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->where('user_id', auth()->user()->id);
            })
            ->paginate(50);

        return view('livewire.client.archive', ['tracks' => $tracks])
            ->layout('livewire.client.layout');
    }
}
