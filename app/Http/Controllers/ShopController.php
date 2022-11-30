<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;

use DB;
use URL;

use App\Utilities\AbstractFilter\ProductFilter;
use App\Models\Page;
use App\Models\Mode;
use App\Models\Post;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Section;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Project;
use App\Models\Category;

class ShopController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', '/')->first();
        $modes = Mode::whereIn('slug', ['new', 'hot'])->first();
        $banners = Banner::where('status', 1)->take(3)->get();
        $posts = Post::where('status', 1)->take(10)->get();
        $relevant_categories = Category::where('status', 2)->get();
        $features = Section::where('slug', 'features')->where('status', 1)->first();

        return view('index', compact('page', 'posts', 'modes', 'features', 'banners', 'relevant_categories'));
    }

    public function brandProducts(Request $request, $company_slug)
    {
        $company = Company::where('slug', $company_slug)->firstOrFail();
        $products = Product::where('status', '<>', 0)->where('company_id', $company->id)->paginate(18);
        $category_list = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.id', 'categories.slug', 'categories.title')
            ->where('company_id', $company->id)
            ->distinct()
            ->get();

        return view('products-brand')->with(['company' => $company, 'products' => $products, 'category_list' => $category_list]);
    }

    public function brandCategoryProducts(Request $request, $company_slug, $category_slug, $category_id)
    {
        $company = Company::where('slug', $company_slug)->firstOrFail();
        $category = Category::findOrFail($category_id);
        $products = Product::where('status', '<>', 0)->where('company_id', $company->id)->where('category_id', $category_id)->paginate(18);
        $category_list = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.id', 'categories.slug', 'categories.title')
            ->where('company_id', $company->id)
            ->distinct()
            ->get();

        return view('products-brand-category')->with(['company' => $company, 'category' => $category, 'products' => $products, 'category_list' => $category_list]);
    }

    public function projectProducts(Request $request, $project_slug, $project_id)
    {
        $project = Project::findOrFail($project_id);

        return view('models')->with(['project' => $project]);
    }

    public function subProjectProducts(Request $request, $project_slug, $subproject_slug, $project_id)
    {
        $project = Project::findOrFail($project_id);

        $ids = $project->descendants->pluck('id');
        $ids[] = $project->id;
        $appends = [];

        /*$sql_query = collect($request->query())
            ->map(fn ($value) => collect($value)) // ->recursive()
            ->map(fn ($value, $key) => $key.' IN ('.$value->map(fn ($value) => $value)->join(', ').')')
            ->except('page')
            ->flatten()
            ->join(' AND ');*/

        $query = Product::where('status', '<>', 0);
        $products_all = $query->whereIn('project_id', $ids)->get();

        if ($request->has('type_id')) {
            $query->whereIn('type', $request->type_id);
        }

        if ($request->has('project_id')) {
            $query->whereIn('project_id', $request->project_id);
        }
        else {
            $query->whereIn('project_id', $ids);
        }

        if ($request->has('category_id')) {
            $query->whereIn('category_id', $request->category_id);
        }

        if ($request->has('option_id')) {
            $option_id = $request->option_id;
            $query->whereHas('options', function ($query_option) use ($option_id) {
                    $query_option->whereIn('option_id', $option_id);
                });
        }

        $products = $query->paginate(18);
        $products->appends($request->query());

        if ($request->ajax()) {
            return response()->json([
                    'products' => view('products-render', ['products' => $products])->render(),
                    'count' => view('partials.products-count', ['products' => $products])->render(),
                ]);
        }

        $categories_ids_boot = array_unique($products_all->pluck('category_id')->toArray());

        $categories_project = Category::where('status', '<>', 0)->whereIn('id', $categories_ids_boot)->get();

        $options = DB::table('products')
            ->join('product_option', 'products.id', '=', 'product_option.product_id')
            ->join('options', 'options.id', '=', 'product_option.option_id')
            ->select('options.id', 'options.slug', 'options.title', 'options.data')
            ->whereIn('project_id', $ids)
            ->where('products.status', '<>', 0)
            ->distinct()
            ->get();

        $grouped = $options->groupBy('data');

        return view('products-project')->with(['project' => $project, 'products' => $products, 'categories_project' => $categories_project, 'grouped' => $grouped]);
        // return $this->projectProducts($request, $subproject_slug, $project_id);
    }

    public function categoryProducts(Request $request, $category_slug, $category_id)
    {
        $category = Category::findOrFail($category_id);

        $ids = $category->descendants->where('status', '!=', 0)->pluck('id');
        $ids[] = $category_id;
        $appends = [];

        $query = Product::where('status', '<>', 0);
        $products_all = $query->whereIn('category_id', $ids)->get();

        foreach($request->query() as $key => $query_id) {

            switch ($key) {

                case 'type_id':
                    $query->whereIn('type', $request->type_id);
                    break;

                case 'category_id':
                    return ($request->has('category_id'))
                        ? $query->whereIn('category_id', $request->category_id)
                        : $query->whereIn('category_id', $ids);
                    break;

                case 'project_id':
                    $query->whereIn('project_id', $request->project_id);
                    break;

                case 'option_id':
                    $option_id = $request->option_id;
                    $query->whereHas('options', function ($query_option) use ($option_id) {
                            $query_option->whereIn('option_id', $option_id);
                        });
                    break;
            }
        }

        // if ($request->has('type_id')) {
        //     $query->whereIn('type', $request->type_id);
        // }

        // if ($request->has('category_id')) {
        //     $query->whereIn('category_id', $request->category_id);
        // }
        // else {
        //     $query->whereIn('category_id', $ids);
        // }

        // if ($request->has('project_id')) {
        //     $query->whereIn('project_id', $request->project_id);
        // }

        // if ($request->has('option_id')) {
        //     $option_id = $request->option_id;
        //     $query->whereHas('options', function ($query_option) use ($option_id) {
        //             $query_option->whereIn('option_id', $option_id);
        //         });
        // }

        $products = $query->paginate(18);
        $products->appends($request->query());

        if ($request->ajax()) {
            return response()->json([
                'products' => view('products-render', ['products' => $products])->render(),
                'count' => view('partials.products-count', ['products' => $products])->render()
            ]);
        }

        $projects_ids_boot = array_unique($products_all->pluck('project_id')->toArray());

        $projects_category = Project::where('status', '<>', 0)->whereIn('id', $projects_ids_boot)->get();

        $options = DB::table('products')
            ->join('product_option', 'products.id', '=', 'product_option.product_id')
            ->join('options', 'options.id', '=', 'product_option.option_id')
            ->select('options.id', 'options.slug', 'options.title', 'options.data')
            ->whereIn('category_id', $ids)
            ->where('products.status', '<>', 0)
            ->distinct()
            ->get();

        $grouped = $options->groupBy('data');

        return view('products-category')->with(['category' => $category, 'projects_category' => $projects_category, 'products' => $products, 'grouped' => $grouped]);
    }

    public function subCategoryProducts(Request $request, $category_slug, $subcategory_slug, $category_id)
    {
        return $this->categoryProducts($request, $subcategory_slug, $category_id);
    }

    public function product($product_id, $product_slug)
    {
        $product = Product::find($product_id);
        $product->views = $product->views + 1;
        $product->save();

        $category = Category::where('id', $product->category_id)->firstOrFail();
        $products = Product::search($product->title)->where('status', 1)->take(4)->get();

        return view('product')->with(['product' => $product, 'category' => $category, 'products' => $products]);
    }

    public function saveComment(Request $request)
    {
        $this->validate($request, [
            'stars' => 'required|integer|between:1,5',
            'comment' => 'required|min:5|max:500',
        ]);

        $url = explode('/', URL::previous());
        $uri = explode('-', end($url));

        if ($request->id == $uri[0]) {

            $comment = new Comment;
            $comment->parent_id = $request->id;
            $comment->parent_type = 'App\Product';
            $comment->name = \Auth::user()->name;
            $comment->email = \Auth::user()->email;
            $comment->comment = $request->comment;
            $comment->stars = (int) $request->stars;
            $comment->save();

            return redirect()->back()->with('status', 'Отзыв добавлен!');
        }

        return redirect()->back()->with('status', 'Ошибка!');
    }
}
