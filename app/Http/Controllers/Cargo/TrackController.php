<?php

namespace App\Http\Controllers\Cargo;

use App\Http\Controllers\Cargo\Controller;
use Illuminate\Http\Request;

use App\Models\Track;
use App\Models\Status;

class TrackController extends Controller
{
    public function index()
    {
        $tracks = Track::orderBy('id', 'desc')->paginate(50);

        return view('cargo.tracks.index', compact('tracks'));
    }

    public function create($lang)
    {
        $statuses = Status::get();

        return view('cargo.tracks.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:tracks',
        ]);

        $track = new Track;
        $track->user_id = $request->user_id;
        $track->code = $request->code;
        $track->description = $request->description;
        $track->lang = $request->lang;
        $track->status = $request->status;
        $track->save();

        return redirect($request->lang.'/admin/tracks')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $track = Track::findOrFail($id);
        $statuses = Status::get();

        return view('cargo.tracks.edit', compact('track', 'statuses'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $track = Track::findOrFail($id);

        $track->user_id = $request->user_id;
        $track->code = $request->code;
        $track->description = $request->description;
        $track->lang = $request->lang;
        $track->status = $request->status;
        $track->save();

        return redirect($lang.'/admin/tracks')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $track = Track::find($id);

        $track->delete();

        return redirect($lang.'/admin/tracks')->with('status', 'Запись удалена!');
    }
}
