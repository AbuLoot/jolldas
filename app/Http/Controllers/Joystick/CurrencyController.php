<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Currency::class);

        $currencies = Currency::orderBy('sort_id')->get();

        return view('joystick.currencies.index', compact('currencies'));
    }

    public function create($lang)
    {
        $this->authorize('create', Currency::class);

        return view('joystick.currencies.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Currency::class);

        $this->validate($request, [
            'currency' => 'required|min:2|max:80|unique:currencies',
        ]);

        $currency = new Currency;

        $currency->sort_id = $request->sort_id;
        $currency->country = $request->country;
        $currency->currency = $request->currency;
        $currency->code = $request->code;
        $currency->symbol = $request->symbol;
        $currency->lang = $request->lang;
        $currency->save();

        return redirect($request->lang.'/admin/currencies')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $currency = Currency::findOrFail($id);

        $this->authorize('update', $currency);

        return view('joystick.currencies.edit', compact('currency'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'currency' => 'required|min:2|max:80',
        ]);

        $currency = Currency::findOrFail($id);

        $this->authorize('update', $currency);

        $currency->sort_id = $request->sort_id;
        $currency->country = $request->country;
        $currency->currency = $request->currency;
        $currency->code = $request->code;
        $currency->symbol = $request->symbol;
        $currency->lang = $request->lang;
        $currency->save();

        return redirect($lang.'/admin/currencies')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $currency = Currency::find($id);

        $this->authorize('delete', $currency);

        $currency->delete();

        return redirect($lang.'/admin/currencies')->with('status', 'Запись удалена!');
    }
}