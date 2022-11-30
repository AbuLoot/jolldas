<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Section;

class SectionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Section::class);

        $sections = Section::orderBy('sort_id')->get();

        return view('joystick.sections.index', compact('sections'));
    }

    public function create($lang)
    {
        $this->authorize('create', Section::class);

        return view('joystick.sections.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Section::class);

        $this->validate($request, [
            'title' => 'required|min:5|max:80|unique:sections',
        ]);

        $data = [];

        for ($i = 0; $i < count($request->data['key']); $i++) {
            $data[$i]['key'] = $request->data['key'][$i];
            $data[$i]['value'] = $request->data['value'][$i];
        }

        $section = new Section;

        $section->sort_id = ($request->sort_id > 0) ? $request->sort_id : $section->count() + 1;
        $section->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $section->title = $request->title;
        $section->image = NULL;
        $section->images = NULL;
        $section->data_1 = serialize($data[0]);
        $section->data_2 = serialize($data[1]);
        $section->data_3 = serialize($data[2]);
        $section->content = $request->content;
        $section->lang = $request->lang;
        $section->status = ($request->status == 'on') ? 1 : 0;
        $section->save();

        return redirect($request->lang.'/admin/sections')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $section = Section::findOrFail($id);

        $this->authorize('update', $section);

        return view('joystick.sections.edit', compact('section'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $data = [];

        for ($i = 0; $i < count($request->data['key']); $i++) {
            $data[$i]['key'] = $request->data['key'][$i];
            $data[$i]['value'] = $request->data['value'][$i];
        }

        $section = Section::findOrFail($id);

        $this->authorize('update', $section);

        $section->sort_id = ($request->sort_id > 0) ? $request->sort_id : $section->count() + 1;
        $section->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $section->title = $request->title;
        $section->image = NULL;
        $section->images = NULL;
        $section->data_1 = serialize($data[0]);
        $section->data_2 = serialize($data[1]);
        $section->data_3 = serialize($data[2]);
        $section->content = $request->content;
        $section->lang = $request->lang;
        $section->status = ($request->status == 'on') ? 1 : 0;
        $section->save();

        return redirect($lang.'/admin/sections')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $section = Section::find($id);

        $this->authorize('delete', $section);

        $section->delete();

        return redirect($lang.'/admin/sections')->with('status', 'Запись удалена!');
    }
}
