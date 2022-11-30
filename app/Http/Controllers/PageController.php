<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Page;
use App\Models\Post;
use App\Models\Section;

class PageController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', '/')->firstOrFail();
        $posts = Post::orderBy('sort_id')->where('status', 1)->get();
        $promo = Section::where('slug', 'promo')->where('status', 1)->first();

        return view('index')->with(['page' => $page, 'posts' => $posts, 'promo' => $promo]);
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('page')->with('page', $page);
    }

    public function catalogs()
    {
        $page = Page::where('slug', 'catalogs')->firstOrFail();

        $files = Storage::files('file-mananger/catalogs');

        return view('pages.catalogs')->with(['page' => $page, 'files' => $files]);
    }

    public function contacts()
    {
        $page = Page::where('slug', 'contacts')->firstOrFail();

        return view('pages.contacts')->with('page', $page);
    }
}
