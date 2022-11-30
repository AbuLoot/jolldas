@extends('joystick.layout')

@section('content')

  <h2 class="page-header">Роли</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/roles/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </p>
  <div class="table-responsive">
    <table class="table-admin table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Название</td>
          <td>Метка</td>
          <td>Описание</td>
          <td>Права</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        @foreach ($roles as $role)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $role->name }}</td>
            <td>{{ $role->display_name }}</td>
            <td>{{ $role->description }}</td>
            <td>
              <?php $grouped = $role->permissions->groupBy('display_name'); ?>
              @foreach($grouped as $name => $group)
                <div>
                  @foreach($group as $permission)
                    {{ $permission->description }},
                  @endforeach
                </div>
              @endforeach
            </td>
            <td class="text-right">
              <a class="btn btn-link btn-xs" href="{{ route('roles.edit', [$lang, $role->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form method="POST" action="{{ route('roles.destroy', [$lang, $role->id]) }}" accept-charset="UTF-8" class="btn-delete">
                <input name="_method" type="hidden" value="DELETE">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

@endsection