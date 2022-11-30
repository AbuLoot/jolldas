<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;

use App\Models\Language;

class LanguageController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', language::class);

        $languages = Language::orderBy('sort_id')->get();

        return view('joystick.languages.index', compact('languages'));
    }

    public function create($lang)
    {
        $this->authorize('create', language::class);

        return view('joystick.languages.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', language::class);

        $this->validate($request, [
            'title' => 'required|max:80|unique:languages',
        ]);

        $language = new Language;

        $language->sort_id = ($request->sort_id > 0) ? $request->sort_id : $language->count() + 1;
        $language->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $language->title = $request->title;
        $language->save();

        return redirect($request->lang.'/admin/languages')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $language = Language::findOrFail($id);

        $this->authorize('update', $language);

        return view('joystick.languages.edit', compact('language'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|max:80',
        ]);

        $language = Language::findOrFail($id);

        $this->authorize('update', $language);

        $language->sort_id = ($request->sort_id > 0) ? $request->sort_id : $language->count() + 1;
        $language->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $language->title = $request->title;
        $language->save();

        return redirect($lang.'/admin/languages')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $language = Language::find($id);

        $this->authorize('delete', $language);

        $language->delete();

        return redirect($lang.'/admin/languages')->with('status', 'Запись удалена!');
    }
}
