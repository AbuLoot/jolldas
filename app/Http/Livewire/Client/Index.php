<?php

namespace App\Http\Livewire\Client;

use Livewire\Component;

use App\Models\Track;

class Index extends Component
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

    public function render()
    {
        $tracks = Track::orderBy('id', 'desc')
            ->when((strlen($this->search) >= 2), function($query) {
                $query->where('code', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->paginate(50);

        return view('livewire.client.index', ['tracks' => $tracks])
            ->layout('livewire.client.layout');
    }
}
