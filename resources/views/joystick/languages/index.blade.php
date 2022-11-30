@extends('joystick.layout')

@section('content')

  <h2 class="page-header">Языки</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/languages/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </p>
  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>URI</td>
          <td>Название</td>
          <td>Номер</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        @foreach ($languages as $language)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $language->slug }}</td>
            <td>{{ $language->title }}</td>
            <td>{{ $language->sort_id }}</td>
            <td class="text-right">
              <a class="btn btn-link btn-xs" href="{{ route('languages.edit', [$lang, $language->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form method="POST" action="{{ route('languages.destroy', [$lang, $language->id]) }}" accept-charset="UTF-8" class="btn-delete">
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