<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

use DB;
use Auth;
use Hash;
use Str;

use App\Models\User;
use App\Models\Region;
use App\Models\Product;
use App\Models\Order;
use App\Models\Country;
use App\Http\Requests;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        return view('account.profile', compact('user'));
    }

    public function statistics()
    {
        $count_orders = Order::where('user_id', auth()->user()->id)->count();
        $count_products = Product::where('user_id', auth()->user()->id)->count();

        $products_ids = Product::select('id')->where('user_id', auth()->user()->id)->get();
        $product_order = DB::table('product_order')->whereIn('product_id', $products_ids->pluck('id')->toArray())->get();
        $count_users_orders = Order::whereIn('id', $product_order->pluck('order_id')->toArray())->count();

        return view('account.statistics', compact('count_orders', 'count_products', 'count_users_orders'));
    }

    public function orders(Request $request)
    {
        $countries = Country::all();

        if ($request->session()->has('items')) {

            $items = $request->session()->get('items');
            $data_id = collect($items['products_id']);
            $products = Product::whereIn('id', $data_id->keys())->get();
        }

        return view('account.orders', compact('products', 'countries'));
    }

    public function myOrders()
    {
        $user = Auth::user();
        $orders = $user->orders()->orderBy('updated_at', 'desc')->paginate(10);

        return view('account.orders', compact('user', 'orders'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        $regions = Region::orderBy('sort_id')->get()->toTree();

        // $date = [];
        // list($date['year'], $date['month'], $date['day']) = explode('-', $user->profile->birthday);

        return view('account.profile-edit', compact('user', 'regions'));
    }

    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'tel' => ['required', 'string', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'id_client' => ['required', 'string', 'min:11', 'max:15'],
            'region_id' => ['required', 'integer'],
            'address' => ['required', 'string'],
        ]);

        $user = Auth::user();

        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->tel = $request->tel;
        $user->id_client = $request->id_client;
        $user->region_id = $request->region_id;
        $user->address = $request->address;
        // $user->id_name = $request->id_name;
        $user->save();

        // $user->profile->birthday = $request->birthday;
        // $user->profile->about = $request->about;
        // $user->profile->sex = $request->sex;
        // $user->profile->save();

        return redirect(app()->getLocale().'/profile')->with('status', 'Запись обновлена!');
    }

    public function passwordEdit($lang)
    {
        return view('account.change-password');
    }

    public function passwordUpdate(Request $request, $lang)
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

        return redirect(app()->getLocale().'/profile')->with('status', 'Запись обновлена!');
    }

}
