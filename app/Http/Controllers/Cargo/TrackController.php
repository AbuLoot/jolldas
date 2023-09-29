<?php

namespace App\Http\Controllers\Cargo;

use App\Http\Controllers\Cargo\Controller;
use Illuminate\Http\Request;

use App\Models\Track;
use App\Models\Status;
use App\Models\TrackStatus;
use App\Models\Region;
use App\Models\User;

class TrackController extends Controller
{
    public function index()
    {
        $tracks = Track::orderBy('id', 'desc')->paginate(50);

        return view('cargo.tracks.index', compact('tracks'));
    }

    public function search(Request $request, $lang)
    {
        $text = trim(strip_tags($request->text));

        $tracks = Track::query()
            ->orderBy('id', 'desc')
            ->when(strlen($text) >= 2, function($query) use ($text) {
                $query->where('code', 'like', '%'.$text.'%')
                    ->orWhere('description', 'like', '%'.$text.'%');
            })
            ->paginate(50);

        return view('cargo.tracks.index', compact('tracks'));
    }

    public function searchUsers(Request $request, $lang, $trackId)
    {
        $text = trim(strip_tags($request->text));

        $users = User::query()
            ->when(strlen($text) >= 2, function($query) use ($text) {
                $query->where('name', 'like', $text.'%')
                    ->orWhere('lastname', 'like', $text.'%')
                    ->orWhere('email', 'like', $text.'%')
                    ->orWhere('tel', 'like', '%'.$text.'%')
                    ->orWhere('id_client', 'like', '%'.$text.'%')
                    ->take(15);
            }, function($query) {
                $query->take(0);
            })
            ->get();

        if ($users->count() > 0) {
            return view('components.dropdown-users', compact('trackId', 'users'));
        }
    }

    public function pinUser($lang, $trackId, $userId)
    {
        $user = User::findOrFail($userId);

        $track = Track::findOrFail($trackId);
        $track->user_id = $user->id;
        $track->save();

        return redirect()->back();
    }

    public function unpinUser($lang, $id)
    {
        $track = Track::findOrFail($id);
        $track->user_id = null;
        $track->save();

        return redirect()->back();
    }

    public function tracksUser(Request $request, $lang, $id)
    {
        $user = User::findOrFail($id);

        $statusId = $request->status_id;

        $tracks = Track::query()
            ->where('user_id', $user->id)
            ->when($statusId >= 1, function($query) use ($statusId) { // Ajax Request
                $query->where('status', $statusId);
            })
            ->paginate(50);

        if (isset($request->status_id)) {
            return view('components.table-tracks', compact('user', 'tracks'));
        }

        $statuses = Status::get();

        return view('cargo.tracks.user', compact('user', 'tracks', 'statuses'));
    }

    public function create($lang)
    {
        $statuses = Status::get();
        $regions = Region::get()->toTree();

        return view('cargo.tracks.create', compact('statuses', 'regions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:tracks',
        ]);

        $track = new Track;
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
        $regions = Region::get()->toTree();

        return view('cargo.tracks.edit', compact('track', 'statuses', 'regions'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'code' => 'required|min:9|max:80',
        ]);

        $track = Track::findOrFail($id);
        $track->code = $request->code;
        $track->description = $request->description;
        $track->lang = $request->lang;

        if ($track->status != $request->status) {

            $status = Status::findOrFail($request->status);

            $trackStatus = new TrackStatus();
            $trackStatus->track_id = $track->id;
            $trackStatus->status_id = $status->id;
            $trackStatus->region_id = $request->region_id;
            $trackStatus->created_at = now();
            $trackStatus->updated_at = now();
            $trackStatus->save();

            $track->status = $status->id;
        }

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
