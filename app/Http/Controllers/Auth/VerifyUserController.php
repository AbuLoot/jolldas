<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Track;

class VerifyUserController extends Controller
{
    public function view()
    {
        return view('auth.verify-user');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'tel' => ['required', 'string', 'max:15'],
            'region_id' => ['required', 'integer'],
            // 'id_client' => ['required', 'string', 'min:9', 'max:15'],
            // 'trackcode' => ['required', 'string', 'min:9', 'max:20'],
        ]);

        $user = User::query()
            ->where('email', $request->email)
            ->where('region_id', $request->region_id)
            ->where('tel', $request->tel)
            ->orWhere('id_client', $request->id_client)
            ->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('warning', 'Данные не совпадает');
        }

        // $existsTrack = Track::query()
        //     ->where('user_id', $user->id)
        //     ->where('code', 'like', '%'.$request->trackcode.'%')
        //     ->first();

        // if (!$existsTrack) {
        //     return redirect()->back()->withInput()->with('warning', 'Трек-код не совпадает');
        // }

        $request->session()->put('verifiedUser', $user->id);

        return redirect('/change-password');
    }
}
