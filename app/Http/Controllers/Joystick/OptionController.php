<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Option;
use App\Models\Language;

class OptionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Option::class);

        $options = Option::orderBy('sort_id')->paginate(50);
        $languages = Language::get();

        return view('joystick.options.index', compact('options', 'languages'));
    }

    public function create($lang)
    {
        $this->authorize('create', Option::class);

        $languages = Language::get();

        return view('joystick.options.create', ['languages' => $languages]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Option::class);

        $this->validate($request, [
            'title' => 'required|min:1|max:100',
            'data' => 'required|min:1|max:100',
        ]);

        $titles = [];
        $data = [];
        $languages = [];

        $option = new Option;

        $titles[$request->lang]['title'] = $request->title;
        $data[$request->lang]['data'] = $request->data;
        $languages[$request->lang] = $request->lang;

        // dd($request, json_encode($titles), json_encode($data), json_encode($languages));

        $option->sort_id = ($request->sort_id > 0) ? $request->sort_id : $option->count() + 1;
        $option->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $option->title = json_encode($titles);
        $option->data = json_encode($data);
        $option->lang = json_encode($languages);
        $option->save();

        return redirect($request->lang.'/admin/options')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $option = Option::findOrFail($id);

        $this->authorize('update', $option);

        $languages = json_decode($option->lang, true);

        if ( ! in_array($lang, $languages)) {
            return view('joystick.options.create-lang', compact('option'));
        }

        return view('joystick.options.edit', compact('option'));
    }

    public function update(Request $request, $lang, $id)
    {
        $option = Option::findOrFail($id);

        $this->authorize('update', $option);

        $titles = json_decode($option->title, true);
        $data = json_decode($option->data, true);
        $languages = json_decode($option->lang, true);

        $titles[$lang]['title'] = $request->title;
        $data[$lang]['data'] = $request->data;
        $languages[$lang] = $lang;

        if (empty($request->title)) {
            unset($titles[$lang]['title']);
            unset($data[$lang]['data']);
            unset($languages[$lang]);
        }

        $option->sort_id = ($request->sort_id > 0) ? $request->sort_id : $option->count() + 1;
        $option->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $option->title = json_encode($titles);
        $option->data = json_encode($data);
        $option->lang = json_encode($languages);
        $option->save();

        return redirect($lang.'/admin/options')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $option = Option::find($id);

        $this->authorize('delete', $option);

        $option->delete();

        return redirect($lang.'/admin/options')->with('status', 'Запись удалена!');
    }
}
