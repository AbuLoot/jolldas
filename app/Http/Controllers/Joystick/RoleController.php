<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Controllers\Joystick\Controller;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Role::class);

    	$roles = Role::all();

        return view('joystick.roles.index', compact('roles'));
    }

    public function create($lang)
    {
        $this->authorize('create', Role::class);

        $permissions = Permission::all();

        return view('joystick.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $this->validate($request, [
            'name' => 'required|max:60|unique:roles',
        ]);

        $role = new Role;
        $role->name = $request->name;
        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $role->save();

        $role->permissions()->sync($request->permissions_id);

        return redirect($request->lang.'/admin/roles')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $role = Role::findOrFail($id);

        $this->authorize('update', $role);

        $permissions = Permission::all();

        return view('joystick.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:60',
        ]);

        $role = Role::findOrFail($id);

        $this->authorize('update', $role);

        $role->name = $request->name;
        $role->display_name = $request->display_name;
        $role->description = $request->description;
        if ($request->permissions_id == null) {
            $role->permissions()->detach($role->permissions_id);
        } else {
            $role->permissions()->sync($request->permissions_id);
        }
        $role->save();

        return redirect($lang.'/admin/roles')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        if (! Gate::allows('crud-user', \Auth::user())) {
            abort(403);
        }

        $role = Role::findOrFail($id);

        $this->authorize('delete', $role);

        $role->delete();

        return redirect($lang.'/admin/roles')->with('status', 'Запись удалена!');
    }
}