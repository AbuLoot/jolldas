<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Mode;
use App\Models\Language;

class ModeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Mode::class);

        $modes = Mode::orderBy('sort_id')->paginate(50);

        return view('joystick.modes.index', compact('modes'));
    }

    public function create($lang)
    {
        $this->authorize('create', Mode::class);

        $languages = Language::get();

        return view('joystick.modes.create', ['languages' => $languages]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Mode::class);

        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:modes',
        ]);

        $mode = new Mode;
        $titles[$request->lang]['title'] = $request->title;
        $languages[$request->lang] = $request->lang;

        $mode->sort_id = ($request->sort_id > 0) ? $request->sort_id : $mode->count() + 1;
        $mode->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $mode->title = serialize($titles);
        $mode->data = $request->data;
        $mode->lang = serialize($languages);
        $mode->status = ($request->status == 'on') ? 1 : 0;
        $mode->save();

        return redirect($request->lang.'/admin/modes')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $mode = Mode::findOrFail($id);

        $this->authorize('update', $mode);

        $languages = unserialize($mode->lang);

        if ( ! in_array($lang, $languages)) {
            return view('joystick.modes.create-lang', compact('mode'));
        }

        return view('joystick.modes.edit', compact('mode'));
    }

    public function update(Request $request, $lang, $id)
    {
        $mode = Mode::findOrFail($id);

        $this->authorize('update', $mode);

        $titles = unserialize($mode->title);
        $languages = unserialize($mode->lang);

        $titles[$lang]['title'] = $request->title;
        $languages[$lang] = $lang;

        if (empty($request->title)) {
            unset($titles[$lang]['title']);
            unset($languages[$lang]);
        }

        $mode->sort_id = ($request->sort_id > 0) ? $request->sort_id : $mode->count() + 1;
        $mode->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $mode->title = serialize($titles);
        $mode->data = $request->data;
        $mode->lang = serialize($languages);
        $mode->status = ($request->status == 'on') ? 1 : 0;
        $mode->save();

        return redirect($lang.'/admin/modes')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $mode = Mode::find($id);

        $this->authorize('delete', $mode);

        $mode->delete();

        return redirect($lang.'/admin/modes')->with('status', 'Запись удалена!');
    }
}
