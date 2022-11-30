<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Session;
use App\Models\Product;

class FavouriteController extends Controller
{
	public function clearFavorite()
	{
		$request->session()->forget('favorite');

		return redirect('/');
	}

	public function toggleFavourite(Request $request, $id)
	{
		if ($request->session()->has('favorite')) {

			$favorite = $request->session()->get('favorite');

			if (in_array($id, $favorite['products_id'])) {
				$status = false;
				unset($favorite['products_id'][$id]);
			}
			else {
				$status = true;
				$favorite['products_id'][$id] = $id;
			}

			$count = count($favorite['products_id']);
			$request->session()->put('favorite', $favorite);

			return response()->json(['id' => $id, 'status' => $status, 'countFavorite' => $count]);
		}

		$favorite = [];
		$favorite['products_id'][$id] = $id;

		$request->session()->put('favorite', $favorite);

		return response()->json(['id' => $id, 'status' => true, 'countFavorite' => 1]);
	}

	public function getFavorite(Request $request)
	{
		if ($request->session()->has('favorite')) {

			$favorite = $request->session()->get('favorite');
			$products = Product::whereIn('id', $favorite['products_id'])->get();
		}
		else {
			$products = collect();
		}

		return view('favorite', compact('products'));
	}

	public function destroy($id)
	{
		$favorite = $request->session()->get('favorite');

		unset($favorite['products_id'][$id]);

		$request->session()->put('favorite', $favorite);

		return redirect('favorite');
	}
}