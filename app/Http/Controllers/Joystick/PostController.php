<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Traits\ImageTrait;

use Storage;

class PostController extends Controller
{
    use ImageTrait;

    public function index()
    {
        $this->authorize('viewAny', Post::class);

        $posts = Post::orderBy('sort_id')->paginate(50);

        return view('joystick.posts.index', compact('posts'));
    }

    public function create($lang)
    {
        $this->authorize('create', Post::class);

        $pages = Page::orderBy('sort_id')->get()->toTree();

        return view('joystick.posts.create', ['pages' => $pages]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:posts',
            'meta_title' => 'required|min:2',
        ]);

        $post = new Post;
        $post->sort_id = ($request->sort_id > 0) ? $request->sort_id : $post->count() + 1;
        // $post->page_id = $request->page_id;
        $post->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $post->title = $request->title;
        $post->headline = $request->headline;
        $post->video = $request->headline;

        if ($request->hasFile('image')) {

            $imageName = $request->image->getClientOriginalExtension();

            // Creating present images
            $this->resizeImage($request->image, 370, 240, '/img/posts/present-'.$imageName, 100);

            // Storing original images
            $this->resizeImage($request->image, 1024, 768, '/img/posts/'.$imageName, 90);

            $post->image = $imageName;
        }

        $post->meta_title = $request->meta_title;
        $post->meta_description = $request->meta_description;
        $post->content = $request->content;
        $post->lang = $request->lang;
        $post->status = ($request->status == 'on') ? 1 : 0;
        $post->save();

        return redirect($request->lang.'/admin/posts')->with('status', 'Запись добавлена.');
    }

    public function edit($lang, $id)
    {
        $post = Post::findOrFail($id);

        $this->authorize('update', $post);

        $pages = Page::orderBy('sort_id')->get()->toTree();

        return view('joystick.posts.edit', compact('post', 'pages'));
    }

    public function update(Request $request, $lang, $id)
    {    	
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
            'meta_title' => 'required|min:2',
        ]);

        $post = Post::findOrFail($id);

        $this->authorize('update', $post);

        $post->sort_id = ($request->sort_id > 0) ? $request->sort_id : $post->count() + 1;
        // $post->page_id = $request->page_id;
        $post->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $post->title = $request->title;
        $post->headline = $request->headline;
        $post->video = $request->video;

        if ($request->hasFile('image')) {

            if ($post->image != NULL AND file_exists('img/posts/'.$post->image)) {
                Storage::delete('img/posts/present-'.$post->image);
                Storage::delete('img/posts/'.$post->image);
            }

            $imageName = $request->image->getClientOriginalName();

            // Creating present images
            $this->resizeImage($request->image, 370, 240, '/img/posts/present-'.$imageName, 100);

            // Storing original images
            $this->resizeImage($request->image, 1024, 768, '/img/posts/'.$imageName, 90);

            $post->image = $imageName;
        }

        $post->meta_title = $request->meta_title;
        $post->meta_description = $request->meta_description;
        $post->content = $request->content;
        $post->lang = $request->lang;
        $post->status = ($request->status == 'on') ? 1 : 0;
        $post->save();

        return redirect($lang.'/admin/posts')->with('status', 'Запись обновлена.');
    }

    public function destroy($lang, $id)
    {
        $post = Post::find($id);

        $this->authorize('delete', $post);

        if (file_exists('img/posts/'.$post->image)) {
            Storage::delete('img/posts/present-'.$post->image);
            Storage::delete('img/posts/'.$post->image);
        }

        $post->delete();

        return redirect($lang.'/admin/posts')->with('status', 'Запись удалена.');
    }
}