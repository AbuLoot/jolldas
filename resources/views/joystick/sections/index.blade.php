@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Разделы</h2>

  @include('components.alerts')
  <p class="text-right">
    <a href="/{{ $lang }}/admin/sections/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </p>
  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Название</td>
          <td>Slug</td>
          <td>Номер</td>
          <td>Язык</td>
          <td>Статус</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        @foreach ($sections as $section)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $section->title }}</td>
            <td>{{ $section->slug }}</td>
            <td>{{ $section->sort_id }}</td>
            <td>{{ $section->lang }}</td>
            <td class="text-{{ trans('statuses.data.'.$section->status.'.style') }}">{{ trans('statuses.data.'.$section->status.'.title') }}</td>
            <td class="text-right text-nowrap">
              <a class="btn btn-link btn-xs" href="{{ route('sections.edit', [$lang, $section->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form class="btn-delete" method="POST" action="{{ route('sections.destroy', [$lang, $section->id]) }}" accept-charset="UTF-8">
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