<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

use App\Models\User;

class ChangePasswordController extends Controller
{
    public function view()
    {
        return view('auth.change-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::find(session('verifiedUser'));

        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        session()->forget('verifiedUser');

        return redirect()->route('login');
    }
}
