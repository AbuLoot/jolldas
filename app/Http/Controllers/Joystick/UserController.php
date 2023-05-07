<?php

namespace App\Http\Controllers\Joystick;

use Auth;
use Hash;

use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Joystick\Controller;

use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use App\Models\Region;
use App\Models\Company;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::orderBy('created_at')->paginate(50);
        $regions = Region::get();

        return view('joystick.users.index', compact('users', 'regions'));
    }

    public function search(Request $request)
    {
        $text = trim(strip_tags($request->text));
        $regionId = $request->region_id ?? 0;

        $users = User::query()
            ->when($regionId >= 1, function($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })
            ->when(strlen($text) >= 2, function($query) use ($text) {
                $query->where('name', 'like', $text.'%');
                $query->orWhere('lastname', 'like', $text.'%');
                $query->orWhere('email', 'like', $text.'%');
                $query->orWhere('tel', 'like', '%'.$text.'%');
            })
            ->paginate(50);

        $users->appends([
            'region_id' => $request->region_id,
        ]);

        $regions = Region::get();
        return view('joystick.users.index', compact('users', 'regions'));
    }

    public function searchAjax(Request $request)
    {
        $text = trim(strip_tags($request->text));

        if (auth()->user()->roles()->firstWhere('name', 'admin')) {
            $products = Product::search($text)->where('in_company_id', $this->companyId)->orderBy('updated_at','desc')->take(50)->get();
        }
        else {
            $products = Product::search($text)->where('in_company_id', $this->companyId)->where('user_id', auth()->user()->id)->orderBy('updated_at','desc')->take(50)->get();
        }

        return response()->json($products);
    }

    public function edit($lang, $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        $regions = Region::orderBy('sort_id')->get()->toTree();
        $companies = Company::orderBy('sort_id')->get();
        $roles = Role::all();

        if ($user->profile == null) {
            return view('joystick.users.create', compact('user', 'regions', 'companies', 'roles'));
        }

        return view('joystick.users.edit', compact('user', 'regions', 'companies', 'roles'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:60',
        ]);

        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->tel = $request->tel;
        $user->id_client = $request->id_client;
        $user->id_name = $request->id_name;
        $user->region_id = $request->region_id;
        $user->address = $request->address;
        // $user->balance = $request->balance;
        // $user->is_customer = ($request->is_customer == 'on') ? 1 : 0;
        // $user->is_worker = ($request->is_worker == 'on' OR $request->role_id) ? 1 : 0;
        $user->status = ($request->status == 'on') ? 1 : 0;

        if (is_null($request->role_id)) {
            $user->roles()->detach();
            // $user->is_worker = 0;
        } else {
            $user->roles()->sync($request->role_id);
        }

        $user->save();

        if (!$user->profile) {

            $profile = new Profile;
            $profile->user_id = $user->id;
            $profile->region_id = $request->region_id;
            $profile->company_id = $request->company_id;
            $profile->tel = $request->tel;
            $profile->birthday = $request->birthday;
            $profile->gender = $request->gender ?? null;
            $profile->about = $request->about;
            $profile->is_debtor = ($request->debt_sum > 0) ? 1 : 0;
            $profile->debt_sum = $request->debt_sum;
            $profile->bonus = $request->bonus;
            $profile->discount = $request->discount;
            $profile->save();

            return redirect($lang.'/admin/users')->with('status', 'Запись обновлена!');
        }

        $user->profile->region_id = $request->region_id;
        $user->profile->company_id = $request->company_id;
        $user->profile->tel = $request->tel;
        $user->profile->birthday = $request->birthday;
        $user->profile->gender = $request->gender;
        $user->profile->about = $request->about;
        $user->profile->is_debtor = ($request->debt_sum > 0) ? 1 : 0;
        $user->profile->debt_sum = $request->debt_sum;
        $user->profile->bonus = $request->bonus;
        $user->profile->discount = $request->discount;
        $user->profile->save();

        return redirect($lang.'/admin/users')->with('status', 'Запись обновлена!');
    }

    public function passwordEdit($lang, $id)
    {
        $user = User::findOrFail($id);

        return view('joystick.users.password', compact('user'));
    }

    public function passwordUpdate(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();

        if ($user->email != $request->email) {
            return redirect()->back()->with('danger', 'Email не совпадает!');
        }

        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        return redirect('/admin/users')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $user = User::find($id);

        $this->authorize('delete', $user);

        $user->roles()->detach();

        if ($user->profile) {
            $user->profile->delete();
        }

        $user->delete();

        return redirect($lang.'/admin/users')->with('status', 'Запись удалена.');
    }
}