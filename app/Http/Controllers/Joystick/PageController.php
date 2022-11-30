<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Page::class);

        $pages = Page::orderBy('sort_id')->get()->toTree();

        return view('joystick.pages.index', compact('pages'));
    }

    public function create($lang)
    {
        $this->authorize('create', Page::class);

        $pages = Page::orderBy('sort_id')->get()->toTree();

        return view('joystick.pages.create', ['pages' => $pages]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Page::class);

        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:pages',
        ]);

        $page = new Page;
        $page->sort_id = ($request->sort_id > 0) ? $request->sort_id : $page->count() + 1;
        $page->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $page->title = $request->title;
        $page->image = $request->image;
        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description;
        $page->content = $request->content;
        $page->lang = $request->lang;
        $page->status = ($request->status == 'on') ? 1 : 0;

        $parent_node = Page::find($request->page_id);

        if (is_null($parent_node)) {
            $page->saveAsRoot();
        }
        else {
            $page->appendToNode($parent_node)->save();
        }

        $page->save();

        return redirect($request->lang.'/admin/pages')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $page = Page::findOrFail($id);

        $this->authorize('update', $page);

        $pages = Page::orderBy('sort_id')->get()->toTree();

        return view('joystick.pages.edit', compact('page', 'pages'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $page = Page::findOrFail($id);

        $this->authorize('update', $page);

        $page->sort_id = ($request->sort_id > 0) ? $request->sort_id : $page->count() + 1;
        $page->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $page->title = $request->title;
        $page->image = $request->image;
        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description;
        $page->content = $request->content;
        $page->lang = $request->lang;

        $parent_node = Page::find($request->page_id);

        if (is_null($parent_node)) {
            $page->saveAsRoot();
        }
        elseif ($page->id != $request->page_id) {
            $page->appendToNode($parent_node)->save();
        }

        $page->status = ($request->status == 'on') ? 1 : 0;
        $page->save();

        return redirect($lang.'/admin/pages')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $page = Page::find($id);

        $this->authorize('delete', $page);

        $page->delete();

        return redirect($lang.'/admin/pages')->with('status', 'Запись удалена!');
    }
}
