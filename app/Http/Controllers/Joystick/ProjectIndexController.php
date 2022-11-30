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

        return redirect($request->lang.'/admin/projects-index')->with('status', 'Записи индексированы.');
    }

    public function latinize($input)
    {
        $latin = [
            'SH', 'CH', 'ZH', 'YA', 'KN', 'GN', 'NG', 'WR',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        ];

        $cyrillic = [
            'Ш', 'Ч', 'Ж', 'Я', 'Н', 'Н', 'НГ', 'Р',
            'А', 'Б', 'К', 'Д', 'Е', 'Ф', 'ДЖ', 'Г', 'И', 'ДЖ', 'К', 'Л', 'М', 'Н',
            'О', 'П', 'К', 'Р', 'С', 'Т', 'У', 'В', 'В', 'КС', 'И', 'З',
        ];

        return str_ireplace($latin, $cyrillic, $input);

        /*$letters = [
            'А' => 'A', 'Б' => 'B', 'Ц' => 'C', 'Ч' => 'Ch', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Э' => 'E', 'Ф' => 'F',
            'Г' => 'G', 'Х' => 'H', 'И' => 'I', 'Й' => 'Y', 'Я' => 'Ya', 'Ю' => 'Yu', 'К' => 'K', 'Л' => 'L', 'М' => 'M',
            'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Ш' => 'Sh', 'Щ' => 'Shch', 'Т' => 'T', 'У' => 'U',
            'В' => 'V', 'Ы' => 'Y', 'З' => 'Z', 'Ж' => 'Zh', 'Ъ' => '', 'Ь' => '', 'Дж' => 'G',

            'а' => 'a', 'б' => 'b', 'ц' => 'c', 'ч' => 'ch', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'э' => 'e', 'ф' => 'f',
            'г' => 'g', 'х' => 'h', 'и' => 'i', 'й' => 'y', 'я' => 'ya', 'ю' => 'yu', 'к' => 'k', 'л' => 'l', 'м' => 'm',
            'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'ш' => 'sh', 'щ' => 'shch', 'т' => 't', 'у' => 'u',
            'в' => 'v', 'ы' => 'y', 'з' => 'z', 'ж' => 'zh', 'ъ' => '', 'ь' => '', 'дж' => 'g',
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

        return redirect($request->lang.'/admin/projects-index')->with('status', 'Запись добавлена.');
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

        return redirect($lang.'/admin/projects-index')->with('status', 'Запись обновлена.');
    }

    public function destroy($lang, $id)
    {
        $project_index = ProjectIndex::find($id);
        $project_index->delete();
        $project_index->searchable();

        return redirect($lang.'/admin/projects-index')->with('status', 'Запись удалена.');
    }
}
