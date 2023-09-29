<?php

namespace App\Http\Livewire\Client;

use Livewire\Component;

use App\Models\Section;

class SignAnAgreement extends Component
{
    public function toSign()
    {
        auth()->user()->status = 2;
        auth()->user()->save();

        return redirect(app()->getLocale().'/client');
    }

    public function render()
    {
        $agreement = Section::where('slug', 'agreement')->first();

        return view('livewire.client.sign-an-agreement', ['agreement' => $agreement]);
    }
}
