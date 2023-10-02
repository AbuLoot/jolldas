<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

use DB;
use Storage;

use App\Http\Controllers\Joystick\Controller;
use App\Models\Mode;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectIndex;
use App\Models\Product;
use App\Models\Category;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductExtensionController extends Controller
{
    public function joytable()
    {
        if (! Gate::allows('joytable', \Auth::user())) {
            abort(403);
        }

        $this->authorize('viewAny', Product::class);

        $categories = Category::get()->toTree();
        $modes = Mode::all();

        return view('joystick.products.joytable', ['categories' => $categories, 'modes' => $modes]);
    }

    public function joytableUpdate(Request $request, $lang)
    {
        if (! Gate::allows('joytable', \Auth::user())) {
            abort(403);
        }

        $this->validate($request, [
            'id' => 'required|min:2',
            'title' => 'required',
            'price' => 'required|numeric',
        ]);

        $product = Product::findOrFail($request->id);

        $this->authorize('update', $product);

        $product->slug = Str::slug($request->title);
        $product->title = $request->title;
        $product->price = $request->price;
        $product->count = $request->count;
        $product->save();

        $product->searchable();

        return response()->json(['status', 'Товар обновлен!']);
    }

    public function export()
    {
        if (! Gate::allows('export', \Auth::user())) {
            abort(403);
        }

        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    public function importView()
    {
        if (! Gate::allows('import', \Auth::user())) {
            abort(403);
        }

        $companies = Company::orderBy('sort_id')->get();
        $categories = Category::get()->toTree();
        $projects = Project::get()->toTree();

        return view('joystick.products.import', ['companies' => $companies, 'categories' => $categories, 'projects' => $projects]);
    }

    public function import(Request $request, $lang)
    {
        if (! Gate::allows('import', \Auth::user())) {
            abort(403);
        }

        Excel::import(new ProductsImport, $request->file('file'));

        $products = Product::where('status', '<>', '0')->get();
        $products->searchable();

        return redirect($lang.'/admin/products')->with('status', 'Данные добавлены.');
    }

    public function search(Request $request)
    {
        $text = Str::upper(trim(strip_tags($request->text)));

        $text = $this->searchByLatin($text);

        if (auth()->user()->roles()->firstWhere('name', 'admin')) {
            $products = Product::search($text)
                ->orderBy('updated_at','desc')
                ->paginate(50);
        } else {
            $products = Product::search($text)
                ->where('user_id', auth()->user()->id)
                ->orderBy('updated_at','desc')
                ->paginate(50);
        }

        $categories = Category::get()->toTree();
        $modes = Mode::all();

        $products->appends([
            'text' => $request->text,
        ]);

        return view('joystick.products.found', compact('categories', 'text', 'modes', 'products'));
    }

    public function searchAjax(Request $request)
    {
        $text = Str::upper(trim(strip_tags($request->text)));

        $text = $this->searchByLatin($text);

        if (auth()->user()->roles()->firstWhere('name', 'admin')) {
            $products = Product::search($text)
                ->orderBy('updated_at','desc')
                ->take(100)
                ->get();
        } else {
            $products = Product::search($text)
                ->where('user_id', auth()->user()->id)
                ->orderBy('updated_at','desc')
                ->take(100)
                ->get();
        }

        return response()->json($products);
    }

    public function searchByLatin($text)
    {
        $words = explode(' ', $text);

        foreach($words as $key => $word) {

            $project_index = ProjectIndex::search($word)->first();

            if ($project_index) {

                if (preg_match("/^[\w\d\s.,-]*$/", $word)) {
                    $words[$key] = $project_index->original;
                    $i = $key + 1;
                    $words[$i] = $project_index->title;
                } else {
                    $words[$key] = $project_index->original;
                    $i = $key + 1;
                    $words[$i] = $word;
                }

                break;

                // $words[$key] = $project_index->original;

                // if (!preg_match("/^[\w\d\s.,-]*$/", $word)) {
                //     $i = $key + 1;
                //     $words[$i] = $word;
                // }
            }
        }

        return implode(' ', $words);
    }

    public function calcForm()
    {
        if (! Gate::allows('allow-calc', \Auth::user())) {
            abort(403);
        }

        $categories = Category::get()->toTree();

        return view('joystick.products.price-calc', ['categories' => $categories]);
    }

    public function priceUpdate(Request $request)
    {
        if (! Gate::allows('allow-calc', \Auth::user())) {
            abort(403);
        }

        $this->validate($request, [
            'category_id' => 'required|numeric',
        ]);

        $category = Category::find($request->category_id);

        if ($category->children && count($category->children) > 0) {
            $ids = $category->descendants->pluck('id')->toArray();
        }

        $ids[] = $category->id;
        $ids = collect($ids)->sort()->implode(',');

        $sql = 'UPDATE products SET price = ';
        $queries = [];

        foreach($request->all() as $key => $input) {
            switch($key) {
                case 'number':
                    $queries[2] = '(price '.$request->operation.' '.$input.')';
                break;
                case 'round':
                    $round = strtoupper($input);
                    $queries[1] = $round.'(';
                    $queries[3] = ', -1) ';
                break;
            }
        }

        $sql .= collect($queries)->sortKeys()->implode('');
        $sql .= 'WHERE category_id IN ('.$ids.')';

        DB::update($sql);

        $products = Product::where('status', '<>', '0')->get();
        $products->searchable();

        return redirect($request->lang.'/admin/products')->with('status', 'Запись обновлена.');
    }

    public function categoryProducts($lang, $id)
    {
        $categories = Category::get()->toTree();
        $category = Category::find($id);

        if ($category->children && count($category->children) > 0) {
            $ids = $category->descendants->pluck('id');
        }

        $ids[] = $category->id;

        if (auth()->user()->roles()->firstWhere('name', 'admin')) {
            $products = Product::whereIn('category_id', $ids)->orderBy('updated_at','desc')->paginate(50);
        } else {
            $products = Product::where('user_id', auth()->user()->id)->whereIn('category_id', $ids)->orderBy('updated_at','desc')->paginate(50);
        }

        $modes = Mode::all();

        return view('joystick.products.index', ['category' => $category, 'categories' => $categories, 'products' => $products, 'modes' => $modes]);
    }

    public function actionProducts(Request $request)
    {
        // Synchronization Of Count In DB
        if ($request->action == 'synchronize') {

            $productsId = $request->products_id;

            $products = Product::query()
                ->where('status', 1)
                ->when($productsId, function($query) use ($productsId) {
                    $query->whereIn('id', $productsId);
                })
                ->update(['count_web' => DB::raw('count')]);

            return response()->json(['status' => true]);
        }

        $this->validate($request, [
            'products_id' => 'required'
        ]);

        // Setting Status
        if (in_array($request->action, ['0', '1', '2', '3'])) {
            Product::whereIn('id', $request->products_id)->update(['status' => $request->action]);
        }
        elseif($request->action == 'destroy') {
            $this->destroyProducts($request->products_id);
        }
        else {
            $mode = Mode::where('slug', $request->action)->first();
            $products = Product::whereIn('id', $request->products_id)->get();

            foreach ($products as $product) {
                $product->modes()->toggle($mode->id);
            }
        }

        return response()->json(['status' => true]);
    }

    public function destroyProducts($productsId)
    {
        $products = Product::whereIn('id', $productsId)->get();

        $this->authorize('delete', $products->first());

        foreach($products as $product) {

            $images = unserialize($product->images);

            if (!empty($images) AND $product->image != 'no-image-middle.png') {

                $existsProduct = Product::whereNotIn('id', [$product->id])->where('path', $product->path)->first();

                if (!$existsProduct) {
                    Storage::deleteDirectory('img/products/'.$product->path);
                }
            }
        }

        Product::destroy($products->pluck('id'));
    }
}
