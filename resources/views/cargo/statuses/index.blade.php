@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Статусы</h2>

  <p class="text-right">
    <a href="/{{ $lang }}/admin/statuses/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </p>

  @include('components.alerts')

  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Название</td>
          <td>Slug</td>
          <td>Язык</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        <?php foreach ($statuses as $status) : ?>
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $status->title }}</td>
            <td>{{ $status->slug }}</td>
            <td>{{ $status->lang }}</td>
            <td class="text-right text-nowrap">
              <a class="btn btn-link btn-xs" href="/{{ app()->getLocale() }}/admin/statuses/{{ $status->id }}/edit" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form class="btn-delete" method="POST" action="/{{ app()->getLocale() }}/admin/statuses/{{ $status->id }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
@endsection