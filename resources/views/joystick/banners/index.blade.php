@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Баннеры</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/banners/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </p>
  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Позиция текста</td>
          <td>Название</td>
          <td>URI</td>
          <td>Заголовок</td>
          <td>Позиция фона (%)</td>
          <td>Язык</td>
          <td>Статус</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        @foreach ($banners as $banner)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $banner->direction }}</td>
            <td>{{ $banner->title }}</td>
            <td>{{ $banner->slug }}</td>
            <td>{{ $banner->marketing }}</td>
            <td>{{ $banner->sort_id }}</td>
            <td>{{ $banner->lang }}</td>
            @if ($banner->status == 1)
              <th class="text-success">Активен</td>
            @else
              <th class="text-danger">Неактивен</td>
            @endif
            <th class="text-right text-nowrap">
              <a class="btn btn-link btn-xs" href="/{{ $banner->link }}" title="Просмотр товара" target="_blank"><i class="material-icons md-18">link</i></a>
              <a class="btn btn-link btn-xs" href="{{ route('banners.edit', [$lang, $banner->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form method="POST" action="{{ route('banners.destroy', [$lang, $banner->id]) }}" accept-charset="UTF-8" class="btn-delete">
                <input name="_method" type="hidden" value="DELETE">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
              </form>
            </td>
          </tr>
          <tr>
            <td colspan="9">
              <img src="/img/banners/{{ $banner->image }}" class="img-responsive"><br>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $banners->links() }}

@endsection
