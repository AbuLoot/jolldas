<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Region;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'lastname' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //     'tel' => ['required', 'string', 'min:10', 'max:14', 'unique:users'],
        //     'region_id' => ['required', 'integer'],
        //     'address' => ['required', 'string'],
        //     'id_client' => ['max:20', 'unique:users'],
        //     'password' => ['required', 'confirmed', Rules\Password::defaults()],
        // ]);

        $region = Region::find($request->region_id);

        // If is Kazakhstan
        if ($region->parent_id == 1) {

            $tel = str_replace(' ', '', $request->tel);
            $length = strlen($tel);

            if ($length < 10 || $length > 12) {
                return redirect()->back()->withInput()->withErrors([
                        __('validation.between.string', ['attribute' => 'tel', 'min' => '10', 'max' => '12'])
                    ]);
            }

            $symbols = substr($tel, 0, 2);

            $validTel = match ($symbols) {
                '+7' => $tel,
                '87' => substr_replace($tel, '+7', 0, -10),
                '70' => substr_replace($tel, '+7', 0, -10),
                '74' => substr_replace($tel, '+7', 0, -10),
                default => substr_replace($tel, '+7', 0, -10),
            };
        }

        $idClient = 'J7799'.substr($region->slug, 0, 3).substr($request->tel, -5);
        $idClient = Str::upper($idClient);

        $user = User::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'tel' => $validTel,
            'id_client' => $idClient,
            'region_id' => $request->region_id,
            'address' => $request->address,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
