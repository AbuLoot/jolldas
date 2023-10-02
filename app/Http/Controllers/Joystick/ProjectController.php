<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Project;
use App\Models\Company;

class ProjectController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::get()->toTree();
        // $projects = Project::with('descendants')->toTree();

        return view('joystick.projects.index', compact('projects'));
    }

    public function actionProjects(Request $request)
    {
        $this->validate($request, [
            'projects_id' => 'required'
        ]);

        Project::whereIn('id', $request->projects_id)->update(['status' => $request->action]);

        return response()->json(['status' => true]);
    }

    public function create($lang)
    {
        $this->authorize('create', Project::class);

        $projects = Project::get()->toTree();
        $companies = Company::all();

        return view('joystick.projects.create', ['projects' => $projects, 'companies' => $companies]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $project = new Project;
        $project->sort_id = ($request->sort_id > 0) ? $request->sort_id : $project->count() + 1;
        $project->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $project->title = $request->title;
        // $project->title_extra = $request->title_extra;
        $project->company_id = (isset($request->company_id)) ? $request->company_id : 0;
        $project->image = (isset($request->image)) ? $request->image : 'no-image-mini.png';
        $project->meta_title = $request->meta_title;
        $project->meta_description = $request->meta_description;
        $project->lang = $request->lang;
        $project->status = $request->status;
        $project->save();

        $parent_node = Project::find($request->project_id);

        if (is_null($parent_node)) {
            $project->saveAsRoot();
        }
        else {
            $project->appendToNode($parent_node)->save();
        }

        return redirect($request->lang.'/admin/projects')->with('status', 'Запись добавлена.');
    }

    public function edit($lang, $id)
    {
        $project = Project::findOrFail($id);

        $this->authorize('update', $project);

        $projects = Project::get()->toTree();
        $companies = Company::all();

        return view('joystick.projects.edit', compact('project', 'projects', 'companies'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $project = Project::findOrFail($id);

        $this->authorize('update', $project);

        $project->sort_id = ($request->sort_id > 0) ? $request->sort_id : $project->count() + 1;
        $project->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $project->title = $request->title;
        // $project->title_extra = $request->title_extra;
        $project->company_id = (isset($request->company_id)) ? $request->company_id : 0;
        $project->image = (isset($request->image)) ? $request->image : 'no-image-mini.png';
        $project->meta_title = $request->meta_title;
        $project->meta_description = $request->meta_description;
        $project->lang = $request->lang;
        $project->status = $request->status;

        $parent_node = Project::find($request->project_id);

        if (is_null($parent_node)) {
            $project->saveAsRoot();
        }
        elseif ($project->id != $request->project_id) {
            $project->appendToNode($parent_node)->save();
        }

        return redirect($lang.'/admin/projects')->with('status', 'Запись обновлена.');
    }

    public function destroy($lang, $id)
    {
        $project = Project::find($id);

        $this->authorize('delete', $project);

        $project->delete();

        return redirect($lang.'/admin/projects')->with('status', 'Запись удалена.');
    }
}
