<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Region;
use App\Models\Order;

use App\Models\CashDoc;
use App\Models\StoreDoc;
use App\Models\IncomingOrder;
use App\Models\OutgoingDoc;

use Auth;
use Mail;

class CartController extends Controller
{
    public function cart(Request $request)
    {
        $regions = Region::all();
        $products = collect();

        if ($request->session()->has('items')) {

            $items = $request->session()->get('items');
            $data_id = collect($items['products_id']);
            $products = Product::whereIn('id', $data_id->keys())->get();
        }

        return view('cart', compact('products', 'regions'));
    }

    public function checkout(Request $request)
    {
        $regions = Region::get()->toTree();

        $items = $request->session()->get('items');

        if (empty($items)) {
            return redirect('/');
        }

        $data_id = collect($items['products_id']);
        $products = Product::whereIn('id', $data_id->keys())->get();

        return view('checkout', compact('regions', 'products'));
    }

    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->session()->has('items')) {

            $items = $request->session()->get('items');

            $quantity = (isset($request->quantity)) ? $request->quantity : 1;

            $items['products_id'][$id] = [
                'id' => $id, 'quantity' => $quantity, 'slug' => $product->slug, 'title' => $product->title, 'img_path' => $product->path.'/'.$product->image, 'price' => $product->price,
            ];

            if ($items['products_id'][$id]['quantity'] > $product->count_web) {
                return response()->json(['status' => 'wrong']);
            }

            $request->session()->put('items', $items);
            $count = count($items['products_id']);
            $sum_price_items = 0;

            foreach ($items['products_id'] as $item) {
                $sum_price_item = $item['price'] * $item['quantity'];
                $sum_price_items += $sum_price_item;
            }

            return response()->json([
                'alert' => 'Товар обновлен', 'countItems' => $count, 'sumPriceItems' => $sum_price_items, 'quantity' => $request->quantity, 'slug' => $product->slug, 'title' => $product->title, 'img_path' => $product->path.'/'.$product->image, 'price' => $product->price,
            ]);
        }

        $items = [];
        $items['products_id'][$id] = [
            'id' => $id, 'quantity' => 1, 'slug' => $product->slug, 'title' => $product->title, 'img_path' => $product->path.'/'.$product->image, 'price' => $product->price,
        ];

        if ($items['products_id'][$id]['quantity'] > $product->count_web) {
            return response()->json(['status' => 'wrong']);
        }

        $request->session()->put('items', $items);

        return response()->json([
            'alert' => 'Товар обновлен', 'countItems' => 1, 'slug' => $product->slug, 'title' => $product->title, 'img_path' => $product->path.'/'.$product->image, 'price' => $product->price,
        ]);
    }

    public function removeFromCart(Request $request, $id)
    {
        $items = $request->session()->get('items');
        $count = count($items['products_id']);

        if ($count == 1) {
            $count = 0;
            $request->session()->forget('items');
        }
        else {
            unset($items['products_id'][$id]);
            $count = $count - 1;
            $request->session()->put('items', $items);
        }

        return response()->json(['countItems' => $count]);
    }

    public function clearCart(Request $request)
    {
        $request->session()->forget('items');

        return redirect('/');
    }

    public function storeOrder(Request $request)
    {
        $this->validate($request, [
            'surname' => 'required|min:2|max:255',
            'name' => 'required|min:2|max:255',
            // 'email' => 'required|email|max:255',
            'phone' => 'required|min:5',
            'address' => 'required',
            'count' => 'required',
        ]);

        $items = $request->session()->get('items');
        $data_id = collect($items['products_id']);
        $products = Product::whereIn('id', $data_id->keys())->get();

        $sum_count_products = 0;
        $sum_price_products = 0;

        foreach ($products as $product) {

            if ($items['products_id'][$product->id]['quantity'] > $product->count_web) {
                return redirect('cart')->with([
                    'info' => 'Количество товаров ограничено'
                ]);
            }

            $productInCart = Product::findOrFail($product->id);

            $productCount = $product->count_web - $items['products_id'][$product->id]['quantity'];
            $product->count_web = ($productCount < 0) ? 0 : $productCount;
            $product->save();

            // $sum_count_products += $items['products_id'][$product->id]['quantity'];
            $sum_price_products += $items['products_id'][$product->id]['quantity'] * $items['products_id'][$product->id]['price'];
        }

        $order = new Order;
        $order->user_id = ((Auth::check())) ? Auth::id() : 0;
        $order->name = $request->surname.' '.$request->name;
        $order->phone = $request->phone;
        $order->email = $request->email;
        $order->company_name = $request->company_name;
        $order->data_1 = $request->notes;
        $order->data_2 = $request->postcode;
        $order->data_3 = '';
        $order->legal_address = '';
        $order->region_id = ($request->region_id) ? $request->region_id : 0;
        $order->address = $request->address;
        $order->count = serialize($request->count);
        $order->price = $products->sum('price');
        $order->amount = $sum_price_products;
        $order->delivery = 1;
        $order->payment_type = $request->pay;
        $order->save();

        $order->products()->attach($data_id->keys());

        $name = $request->name;

        // Email subject
        $subject = "AutoRex - Новая заявка от $request->name";

        $headers = "From: info@autorex.kz \r\n" .
                   "MIME-Version: 1.0" . "\r\n" . 
                   "Content-type: text/html; charset=UTF-8" . "\r\n";

        $content = view('partials.mail-new-order', ['order' => $order])->render();

        try {
            mail('autorexcom@gmail.com', $subject, $content, $headers);

            $status = 'alert-success';
            $message = 'Ваш заказ принят!';

            // Mail::send('vendor.mail.html.layout', ['order' => $order], function($message) use ($name) {
            //     $message->to(['abdulaziz.abishov@gmail.com', 'issayev.adilet@gmail.com'], 'AutoRex')->subject('AutoRex - Новый заказ от '.$name);
            //     $message->from('electron.servant@gmail.com', 'Electron Servant');
            // });

            $response = view('partials.mail-client-order', ['order' => $order])->render();

            mail($order->email, 'AutoRex - ваш заказ: '.$order->id, $response, $headers);

        } catch (Exception $e) {

            $status = 'alert-danger';
            $message = 'Произошла ошибка: '.$e->getMessage();
        }

        $request->session()->forget('items');

        return redirect('cart')->with([
            'info' => $message
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $items = $request->session()->get('items');

        if (count($items['products_id']) == 1) {
            $request->session()->forget('items');
        }
        else {
            unset($items['products_id'][$id]);
            $request->session()->put('items', $items);
        }

        return redirect('cart');
    }
}