<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Image;
use Storage;

use App\Models\Mode;
use App\Models\Region;
use App\Models\Option;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Project;
use App\Models\Product;
use App\Models\Category;
use App\Models\Currency;
use App\Traits\ImageTrait;

class UserProductController extends Controller
{
    use ImageTrait;

    public function index()
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::where('user_id', auth()->user()->id)->orderBy('updated_at','desc')->paginate(50);

        $categories = Category::get()->toTree();
        $modes = Mode::all();

        return view('account.products.index', ['categories' => $categories, 'products' => $products, 'modes' => $modes]);
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        $currency = Currency::where('lang', 'kz')->first();
        $categories = Category::get()->toTree();
        $companies = Company::orderBy('sort_id')->get();
        $projects = Project::get()->toTree();
        $regions = Region::orderBy('sort_id')->get()->toTree();
        $options = Option::orderBy('sort_id')->get();
        $modes = Mode::all();

        return view('account.products.create', ['modes' => $modes, 'regions' => $regions, 'currency' => $currency, 'categories' => $categories, 'companies' => $companies, 'projects' => $projects, 'options' => $options]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $this->validate($request, [
            'title' => 'required|min:2',
            'project_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            // 'barcode' => 'required',
            // 'images' => 'mimes:jpeg,jpg,png,svg,svgs,bmp,gif',
        ]);

        $introImage = 'no-image-middle.png';
        $images = [];
        $dirName = null;

        if ($request->hasFile('images')) {

            $dirName = $request->project_id.'/'.time();
            Storage::makeDirectory('img/products/'.$dirName);

            $images = $this->saveImages($request, $dirName);
            $introImage = current($images)['present_image'];
        }

        $product = new Product;
        $product->sort_id = ($request->sort_id > 0) ? $request->sort_id : $product->count() + 1;
        $product->user_id = auth()->user()->id;
        $product->category_id = $request->category_id;
        $product->project_id = $request->project_id;
        $product->company_id = $request->company_id;
        $product->slug = Str::slug($request->title);
        $product->title = $request->title;
        $product->meta_title = $request->title;
        // $product->meta_description = $request->title.' '.$request->description;
        $product->barcode = $request->barcode;
        $product->price = $request->price;
        $product->count_web = $request->count;
        $product->type = $request->type;
        $product->area = $request->area;
        $product->description = $request->description;
        $product->characteristic = $request->characteristic;
        $product->parameters = $request->parameters;
        $product->path = $dirName;
        $product->image = $introImage;
        $product->images = serialize($images);
        $product->lang = 'ru';
        $product->status = 0;
        $product->save();

        if ( ! is_null($request->modes_id)) {
            $product->modes()->attach($request->modes_id);
        }

        if ( ! is_null($request->options_id)) {
            $product->options()->attach($request->options_id);
        }

        $product->searchable();

        return redirect('user-products')->with('status', 'Товар добавлен!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);

        $this->authorize('update', $product);

        $categories = Category::get()->toTree();
        $currency = Currency::where('lang', 'kz')->first();
        $companies = Company::orderBy('sort_id')->get();
        $projects = Project::get()->toTree();
        $regions = Region::orderBy('sort_id')->get()->toTree();
        $options = Option::orderBy('sort_id')->get();
        $grouped = $options->groupBy('data');
        $modes = Mode::all();

        return view('account.products.edit', ['modes' => $modes, 'regions' => $regions, 'product' => $product, 'currency' => $currency, 'categories' => $categories, 'companies' => $companies, 'projects' => $projects, 'options' => $options, 'grouped' => $grouped]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2',
            'project_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            // 'barcode' => 'required',
        ]);

        $product = Product::findOrFail($id);

        $this->authorize('update', $product);

        $dirName = $product->path;
        $images = unserialize($product->images);

        // Remove images
        if (isset($request->remove_images)) {
            $images = $this->removeImages($request, $images, $product);
            $introImage = (isset($images[0]['present_image'])) ? $images[0]['present_image'] : 'no-image-middle.png';
            $product->image = $introImage;
            $product->images = serialize($images);
        }

        // Adding new images
        if ($request->hasFile('images')) {

            if ( ! file_exists('img/products/'.$dirName) OR empty($dirName)) {
                $dirName = $product->project_id.'/'.time();
                Storage::makeDirectory('img/products/'.$dirName);
                $product->path = $dirName;
            }

            $images = $this->uploadImages($request, $dirName, $images, $product);
            $introImage = current($images)['present_image'];
            $product->image = $introImage;
            $product->images = serialize($images);
        }

        // Change directory for new category
        if ($product->project_id != $request->project_id AND file_exists('img/products/'.$product->path) AND  $product->path != '') {
            $dirName = $request->project_id.'/'.time();
            Storage::move('img/products/'.$product->path, 'img/products/'.$dirName);
            $product->path = $dirName;
        }

        $product->sort_id = ($request->sort_id > 0) ? $request->sort_id : $product->count() + 1;
        $product->category_id = $request->category_id;
        $product->project_id = $request->project_id;
        $product->company_id = $request->company_id;
        $product->slug = Str::slug($request->title);
        $product->title = $request->title;
        $product->meta_title = $request->title;
        $product->meta_description = $request->title.' '.$request->meta_description;
        $product->barcode = $request->barcode;
        $product->price = $request->price;
        $product->count_web = $request->count;
        $product->type = $request->type;
        $product->area = $request->area;
        $product->description = $request->description;
        $product->characteristic = $request->characteristic;
        $product->parameters = $request->parameters;
        $product->lang = 'ru';
        $product->status = 0;
        $product->save();

        // $product->modes()->sync($request->modes_id);

        // $product->options()->sync($request->options_id);

        $product->searchable();

        return redirect('user-products')->with('status', 'Товар обновлен!');
    }

    public function saveImages($request, $dirName)
    {
        $order = 1;
        $images = [];

        foreach ($request->file('images') as $key => $image)
        {
            $imageName = 'image-'.$order.'-'.Str::slug($request->title).'.'.$image->getClientOriginalExtension();

            $watermark = Image::make('img/watermark.png');

            // Creating present images
            $this->resizeOptimalImage($image, 320, 290, '/img/products/'.$dirName.'/present-'.$imageName, 80);

            // Storing original images
            // $image->storeAs('/img/products/'.$dirName, $imageName);
            $this->resizeOptimalImage($image, 800, 600, '/img/products/'.$dirName.'/'.$imageName, 80, $watermark);

            $images[$key]['image'] = $imageName;
            $images[$key]['present_image'] = 'present-'.$imageName;
            $order++;
        }

        return $images;
    }

    public function uploadImages($request, $dirName, $images = [], $product)
    {
        $order = (!empty($images)) ? count($images) : 1;
        $order = time() + 1;

        foreach ($request->file('images') as $key => $image)
        {
            $imageName = 'image-'.$order.'-'.Str::slug($request->title).'.'.$image->getClientOriginalExtension();

            $watermark = Image::make('img/watermark.png');

            // Creating present images
            $this->resizeOptimalImage($image, 320, 290, '/img/products/'.$dirName.'/present-'.$imageName, 80);

            // Storing original images
            $this->resizeOptimalImage($image, 800, 600, '/img/products/'.$dirName.'/'.$imageName, 80, $watermark);

            if (isset($images[$key])) {

                Storage::delete([
                    'img/products/'.$product->path.'/'.$images[$key]['image'],
                    'img/products/'.$product->path.'/'.$images[$key]['present_image']
                ]);
            }

            $images[$key]['image'] = $imageName;
            $images[$key]['present_image'] = 'present-'.$imageName;
            $order++;
        }

        ksort($images);
        return $images;
    }

    public function removeImages($request, $images = [], $product)
    {
        foreach ($request->remove_images as $kvalue) {

            if (!isset($request->images[$kvalue])) {

                Storage::delete([
                    'img/products/'.$product->path.'/'.$images[$kvalue]['image'],
                    'img/products/'.$product->path.'/'.$images[$kvalue]['present_image']
                ]);

                unset($images[$kvalue]);
            }
        }


        ksort($images);
        return $images;
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $this->authorize('delete', $product);

        $images = unserialize($product->images);

        if (! empty($images) AND $product->image != 'no-image-middle.png') {

            foreach ($images as $image) {
                Storage::delete([
                    'img/products/'.$product->path.'/'.$image['image'],
                    'img/products/'.$product->path.'/'.$image['present_image']
                ]);
            }

            Storage::deleteDirectory('img/products/'.$product->path);
        }

        $product->delete();

        return redirect()->back();
    }
}
