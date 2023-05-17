<?php

namespace App\Http\Controllers\Cargo;

use App\Http\Controllers\Cargo\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Status;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::get();

        return view('cargo.statuses.index', compact('statuses'));
    }

    public function create($lang)
    {
        return view('cargo.statuses.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:statuses',
        ]);

        $status = new Status;
        $status->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $status->title = $request->title;
        $status->lang = $request->lang;
        $status->save();

        return redirect($request->lang.'/admin/statuses')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $status = Status::findOrFail($id);

        return view('cargo.statuses.edit', compact('status'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $status = Status::findOrFail($id);

        $status->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $status->title = $request->title;
        $status->lang = $request->lang;
        $status->save();

        return redirect($lang.'/admin/statuses')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $status = Status::find($id);

        $status->delete();

        return redirect($lang.'/admin/statuses')->with('status', 'Запись удалена!');
    }
}
