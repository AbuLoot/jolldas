<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Category;

use Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::orderBy('sort_id')->get()->toTree();

        return view('joystick.categories.index', compact('categories'));
    }

    public function actionCategories(Request $request)
    {
        $this->validate($request, [
            'categories_id' => 'required'
        ]);

        if (in_array($request->action, ['0', '1', '2', '3'])) {
            Category::whereIn('id', $request->categories_id)->update(['status' => $request->action]);
        }
        elseif($request->action == 'destroy') {
            Category::whereIn('id', $request->categories_id)->delete();
        }

        return response()->json(['status' => true]);
    }

    public function create($lang)
    {
        $this->authorize('create', Category::class);

        $categories = Category::get()->toTree();

        return view('joystick.categories.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Category::class);

        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $category = new Category;
        $category->sort_id = ($request->sort_id > 0) ? $request->sort_id : $category->count() + 1;
        $category->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $category->title = trim($request->title);
        $category->title_extra = $request->title_extra;
        $category->image = (isset($request->image)) ? $request->image : 'no-image-mini.png';
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;

        $parent_node = Category::find($request->category_id);

        if (is_null($parent_node)) {
            $category->saveAsRoot();
        }
        else {
            $category->appendToNode($parent_node)->save();
        }

        $category->lang = $request->lang;
        $category->status = $request->status;
        $category->save();

        return redirect($request->lang.'/admin/categories')->with('status', 'Запись добавлена.');
    }

    public function edit($lang, $id)
    {
        $category = Category::findOrFail($id);

        $this->authorize('update', $category);

        $categories = Category::get()->toTree();

        return view('joystick.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $category = Category::findOrFail($id);

        $this->authorize('update', $category);

        $category->sort_id = ($request->sort_id > 0) ? $request->sort_id : $category->count() + 1;
        $category->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $category->title = trim($request->title);
        $category->title_extra = $request->title_extra;
        $category->image = (isset($request->image)) ? $request->image : 'no-image-mini.png';
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;

        $parent_node = Category::find($request->category_id);

        if (is_null($parent_node)) {
            $category->saveAsRoot();
        }
        elseif ($category->id != $request->category_id) {
            $category->appendToNode($parent_node)->save();
        }

        $category->lang = $request->lang;
        $category->status = $request->status;
        $category->save();

        return redirect($lang.'/admin/categories')->with('status', 'Запись обновлена.');
    }

    public function destroy($lang, $id)
    {
        $category = Category::find($id);

        $this->authorize('delete', $category);

        $category->delete();

        return redirect($lang.'/admin/categories')->with('status', 'Запись удалена.');
    }
}
