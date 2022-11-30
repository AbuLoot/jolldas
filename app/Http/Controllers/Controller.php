<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\Page;
use App\Models\Mode;
use App\Models\Project;
use App\Models\Company;
use App\Models\Section;
use App\Models\Category;
use App\Models\Language;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $pages = Page::where('status', 1)->whereNotIn('slug', ['/'])->orderBy('sort_id')->get()->toTree();
        $sections = Section::whereIn('slug', ['header-code', 'footer-code', 'contacts', 'soc-networks'])->get();
        $companies = Company::where('status', 2)->orderBy('sort_id')->get();
        $projects = Project::where('status', '<>', 0)->orderBy('sort_id')->get()->toTree();
        $categories = Category::orderBy('sort_id')->where('status', '<>', 0)->get()->toTree();

        view()->share([
            'lang' => app()->getLocale(),
            'pages' => $pages,
            'companies' => $companies,
            'projects' => $projects,
            'categories' => $categories,
            'sections' => $sections,
        ]);
    }
}
