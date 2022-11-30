@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/roles" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
  </p>
  <div class="row">
    <div class="col-md-11">
      <div class="panel panel-default">
        <div class="panel-body">
          <form action="{{ route('roles.update', [$lang, $role->id]) }}" method="post">
            <input type="hidden" name="_method" value="PUT">
            {!! csrf_field() !!}

            <div class="form-group">
              <label for="name">Название</label>
              <input type="text" class="form-control" id="name" name="name" maxlength="80" value="{{ (old('name')) ? old('name') : $role->name }}" required>
            </div>
            <div class="form-group">
              <label for="display_name">Метка</label>
              <input type="text" class="form-control" id="display_name" name="display_name" maxlength="80" value="{{ (old('display_name')) ? old('display_name') : $role->display_name }}">
            </div>
            <div class="form-group">
              <label for="description">Описание</label>
              <input type="text" class="form-control" id="description" name="description" maxlength="80" value="{{ (old('description')) ? old('description') : $role->description }}">
            </div>
            <div class="form-group">
              <label>Права доступа:</label><br>
              <?php $grouped = $permissions->groupBy('display_name'); ?>
              @foreach($grouped as $name => $group)
                <div style="display: inline-block; margin-right: 15px;">
                  <h4><b>{{ $name }}</b></h4>
                  @foreach($group as $permission)
                    @if ($role->permissions->contains($permission->id))
                      <label><input type="checkbox" name="permissions_id[]" value="{{ $permission->id }}" checked="checked"> {{ $permission->description }}</label><br>
                    @else
                      <label><input type="checkbox" name="permissions_id[]" value="{{ $permission->id }}"> {{ $permission->description }}</label><br>
                    @endif
                  @endforeach<br>
                </div>
              @endforeach
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-success"><i class="material-icons">save</i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
