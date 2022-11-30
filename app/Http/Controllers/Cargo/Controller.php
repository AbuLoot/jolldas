<?php

namespace App\Http\Controllers\Cargo;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\Language;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        app()->setLocale(\Request::segment(1));

        $languages = Language::orderBy('sort_id')->get();

        view()->share([
            'lang' => app()->getLocale(),
            'languages' => $languages
        ]);
    }
}
