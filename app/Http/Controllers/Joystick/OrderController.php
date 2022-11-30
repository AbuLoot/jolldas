<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Order;
use App\Models\Region;

class OrderController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::orderBy('created_at', 'desc')->paginate(50);

        return view('joystick.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $this->authorize('view', Order::class);

        $order = Order::findOrFail($id);

        return view('joystick.orders.show', compact('order'));
    }

    public function edit($lang, $id)
    {
        $order = Order::findOrFail($id);

        $this->authorize('update', $order);

        $regions = Region::all();

        return view('joystick.orders.edit', compact('order', 'regions'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'name' => 'required|min:2|max:255',
            'phone' => 'required|min:5',
            'region_id' => 'numeric',
            'address' => 'required',
        ]);

        $order = Order::findOrFail($id);

        $this->authorize('update', $order);

        $order->name = $request->surname.' '.$request->name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->company_name = $request->company_name;
        $order->data_1 = $request->data_1;
        $order->data_2 = $request->data_2;
        $order->data_3 = $request->data_3;
        $order->legal_address = $request->legal_address;
        $order->region_id = ($request->region_id) ? $request->region_id : 0;
        $order->address = $request->address;
        $order->delivery = $request->delivery;
        $order->payment_type = $request->payment_type;
        // $order->count = serialize($request->count);
        // $order->price = $products->sum('price');
        // $order->amount = $sumPriceProducts;
        $order->status = $request->status;
        $order->save();

        return redirect($lang.'/admin/orders')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $order = Order::find($id);

        $this->authorize('delete', $order);

        $order->delete();

        return redirect($lang.'/admin/orders')->with('status', 'Запись удалена!');
    }
}