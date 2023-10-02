<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;

use App\Models\Banner;

use Storage;

class BannerController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Banner::class);

        $banners = Banner::orderBy('sort_id')->paginate(50);

        return view('joystick.banners.index', compact('banners'));
    }

    public function create($lang)
    {
        $this->authorize('create', Banner::class);

        return view('joystick.banners.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Banner::class);

        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:banners',
            'image' => 'required',
        ]);

        $banner = new Banner;

        if ($request->hasFile('image')) {

            $imageName = $request->image->getClientOriginalName();

            $request->image->storeAs('img/banners', $imageName);
        }

        $banner->sort_id = ($request->sort_id > 0) ? $request->sort_id : $banner->count() + 1;
        $banner->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $banner->title = $request->title;
        $banner->marketing = $request->marketing;
        $banner->link = $request->link;
        $banner->image = $imageName;
        $banner->lang = $request->lang;
        $banner->status = ($request->status == 'on') ? 1 : 0;
        $banner->direction = $request->direction;
        $banner->color = $request->color;
        $banner->save();

        return redirect($lang.'/admin/banners')->with('status', 'Запись добавлена.');
    }

    public function edit($lang, $id)
    {
        $banner = Banner::findOrFail($id);

        $this->authorize('update', $banner);

        return view('joystick.banners.edit', compact('banner'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $banner = Banner::findOrFail($id);

        $this->authorize('update', $banner);

        if ($request->hasFile('image')) {

            if (file_exists('img/banners/'.$banner->image)) {
                Storage::delete('img/banners/'.$banner->image);
            }

            $imageName = $request->image->getClientOriginalName();

            $request->image->storeAs('img/banners', $imageName);
        }

        $banner->sort_id = ($request->sort_id > 0) ? $request->sort_id : $banner->count() + 1;
        $banner->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $banner->title = $request->title;
        $banner->marketing = $request->marketing;
        $banner->link = $request->link;
        if (isset($imageName)) $banner->image = $imageName;
        $banner->lang = $request->lang;
        $banner->status = ($request->status == 'on') ? 1 : 0;
        $banner->direction = $request->direction;
        $banner->color = $request->color;
        $banner->save();

        return redirect($lang.'/admin/banners')->with('status', 'Запись обновлена.');
    }

    public function destroy($lang, $id)
    {
        $banner = Banner::find($id);

        $this->authorize('delete', $banner);

        if (file_exists('img/banners/'.$banner->image)) {
            Storage::delete('img/banners/'.$banner->image);
        }

        $banner->delete();

        return redirect($lang.'/admin/banners')->with('status', 'Запись удалена.');
    }
}
