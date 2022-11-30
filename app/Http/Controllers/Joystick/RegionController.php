<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Region;

class RegionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Region::class);

        // $regions = Region::orderBy('sort_id')->with('ancestors')->paginate(50)->toTree();
        $regions = Region::orderBy('sort_id')->get()->toTree();

        return view('joystick.regions.index', compact('regions'));
    }

    public function create($lang)
    {
        $this->authorize('create', Region::class);

        $regions = Region::get()->toTree();

        return view('joystick.regions.create', ['regions' => $regions]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Region::class);

        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:regions',
        ]);

        $region = new Region;

        $region->sort_id = ($request->sort_id > 0) ? $request->sort_id : $region->count() + 1;
        $region->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $region->title = $request->title;
        $region->data = $request->data;
        $region->lang = $request->lang;
        $region->status = ($request->status == 'on') ? 1 : 0;

        $parent_node = Region::find($request->region_id);

        if (is_null($parent_node)) {
            $region->makeRoot()->save();
        }
        else {
            $region->appendToNode($parent_node)->save();
        }

        return redirect($request->lang.'/admin/regions')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $region = Region::findOrFail($id);

        $this->authorize('update', $region);

        $regions = Region::get()->toTree();

        return view('joystick.regions.edit', compact('region', 'regions'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $region = Region::findOrFail($id);

        $this->authorize('update', $region);

        $region->sort_id = ($request->sort_id > 0) ? $request->sort_id : $region->count() + 1;
        $region->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $region->title = $request->title;
        $region->data = $request->data;

        $parent_node = Region::find($request->region_id);

        if (is_null($parent_node)) {
            $region->saveAsRoot();
        }
        elseif ($region->id != $request->region_id) {
            $region->appendToNode($parent_node)->save();
        }

        $region->lang = $request->lang;
		$region->status = ($request->status == 'on') ? 1 : 0;
        $region->save();

        return redirect($lang.'/admin/regions')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $region = Region::find($id);

        $this->authorize('delete', $region);

        $region->delete();

        return redirect($lang.'/admin/regions')->with('status', 'Запись удалена!');
    }
}
