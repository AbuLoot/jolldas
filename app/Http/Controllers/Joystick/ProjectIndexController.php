<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Project;
use App\Models\ProjectIndex;

class ProjectIndexController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $projects_index = ProjectIndex::get();

        return view('joystick.projects-index.index', compact('projects_index'));
    }

    public function actionProjects(Request $request)
    {
        $this->validate($request, [
            'projects_id' => 'required'
        ]);

        ProjectIndex::whereIn('id', $request->projects_id)->update(['status' => $request->action]);

        return response()->json(['status' => true]);
    }

    public function indexing(Request $request)
    {
        $projects = Project::get();
        $projects_index = ProjectIndex::get();

        $cyrillic_arr = [];

        foreach ($projects as $key => $project) {

            // Alternative code: preg_match_all('#.{1}#uis', $project->title, $out);
            $arr = str_split($project->title);

            $two_letters = ['SH', 'CH', 'ZH', 'YA', 'KN', 'GN', 'NG', 'WR'];

            foreach ($two_letters as $letter) {

                $pos = strripos($project->title, $letter);

                if ($pos == true) {
                    $char = substr($project->title, $pos, 2);
                    $arr[$pos] = $char;
                    unset($arr[++$pos]);
                    break;
                }
            }

            $cyrillic_arr[$key] = implode('', $this->latinize($arr));

            $project_index = $projects_index->where('title', $cyrillic_arr[$key])->first();

            if (is_null($project_index)) {

                $new_project_index = new ProjectIndex;
                $new_project_index->sort_id = $projects_index->count() + 1;
                $new_project_index->original = $project->title;
                $new_project_index->title = $cyrillic_arr[$key];
                $new_project_index->lang = app()->getLocale();
                $new_project_index->status = 1;
                $new_project_index->save();
                $new_project_index->searchable();
            }
        }

        return redirect($request->lang.'/admin/projects-index')->with('status', '???????????? ??????????????????????????.');
    }

    public function latinize($input)
    {
        $latin = [
            'SH', 'CH', 'ZH', 'YA', 'KN', 'GN', 'NG', 'WR',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        ];

        $cyrillic = [
            '??', '??', '??', '??', '??', '??', '????', '??',
            '??', '??', '??', '??', '??', '??', '????', '??', '??', '????', '??', '??', '??', '??',
            '??', '??', '??', '??', '??', '??', '??', '??', '??', '????', '??', '??',
        ];

        return str_ireplace($latin, $cyrillic, $input);

        /*$letters = [
            '??' => 'A', '??' => 'B', '??' => 'C', '??' => 'Ch', '??' => 'D', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'F',
            '??' => 'G', '??' => 'H', '??' => 'I', '??' => 'Y', '??' => 'Ya', '??' => 'Yu', '??' => 'K', '??' => 'L', '??' => 'M',
            '??' => 'N', '??' => 'O', '??' => 'P', '??' => 'R', '??' => 'S', '??' => 'Sh', '??' => 'Shch', '??' => 'T', '??' => 'U',
            '??' => 'V', '??' => 'Y', '??' => 'Z', '??' => 'Zh', '??' => '', '??' => '', '????' => 'G',

            '??' => 'a', '??' => 'b', '??' => 'c', '??' => 'ch', '??' => 'd', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'f',
            '??' => 'g', '??' => 'h', '??' => 'i', '??' => 'y', '??' => 'ya', '??' => 'yu', '??' => 'k', '??' => 'l', '??' => 'm',
            '??' => 'n', '??' => 'o', '??' => 'p', '??' => 'r', '??' => 's', '??' => 'sh', '??' => 'shch', '??' => 't', '??' => 'u',
            '??' => 'v', '??' => 'y', '??' => 'z', '??' => 'zh', '??' => '', '??' => '', '????' => 'g',
        ];

        return stristr($letters, $input[0]);*/
    }

    public function create($lang)
    {
        $this->authorize('create', Project::class);

        return view('joystick.projects-index.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $project_index = new ProjectIndex;
        $project_index->sort_id = ($request->sort_id > 0) ? $request->sort_id : $project_index->count() + 1;
        $project_index->original = (empty($request->original)) ? Str::slug($request->title) : $request->original;
        $project_index->title = $request->title;
        $project_index->lang = $request->lang;
        $project_index->status = $request->status;
        $project_index->save();
        $project_index->searchable();

        return redirect($request->lang.'/admin/projects-index')->with('status', '???????????? ??????????????????.');
    }

    public function edit($lang, $id)
    {
        $project_index = ProjectIndex::findOrFail($id);

        return view('joystick.projects-index.edit', compact('project_index'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $project_index = ProjectIndex::findOrFail($id);
        $project_index->sort_id = ($request->sort_id > 0) ? $request->sort_id : $project_index->count() + 1;
        $project_index->title = $request->title;
        $project_index->original = (empty($request->original)) ? Str::slug($request->title) : $request->original;
        $project_index->lang = $request->lang;
        $project_index->status = $request->status;
        $project_index->save();

        // dd($project_index);
        $project_index->searchable();

        return redirect($lang.'/admin/projects-index')->with('status', '???????????? ??????????????????.');
    }

    public function destroy($lang, $id)
    {
        $project_index = ProjectIndex::find($id);
        $project_index->delete();
        $project_index->searchable();

        return redirect($lang.'/admin/projects-index')->with('status', '???????????? ??????????????.');
    }
}
