@extends('joystick.layout')

@section('content')

  <h2 class="page-header">Опции</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/options/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </p>
  <div class="table-responsive">
    <table class="table table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>URI</td>
          <td>Название</td>
          <td>Номер</td>
          <td>Язык</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        <?php $grouped = $options->groupBy('data'); ?>
        @foreach ($grouped as $data => $group)
          <th class="active" colspan="6">
            <?php $data = json_decode($data, true); ?>
            @foreach ($data as $key => $value)
              {{ $data[$key]['data'] }} |
            @endforeach
          </th>
          @foreach ($group as $option)
            <?php $titles = json_decode($option->title, true); ?>
            <?php $languages = json_decode($option->lang, true); ?>
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ $option->slug }}</td>
              <td>
                @foreach ($languages as $language)
                  {{ $titles[$language]['title'] }}<br>
                @endforeach
              </td>
              <td>{{ $option->sort_id }}</td>
              <td>
                @foreach ($languages as $language)
                  {{ $language }}<br>
                @endforeach
              </td>
              <td class="text-right">
                <a class="btn btn-link btn-xs" href="{{ route('options.edit', [$lang, $option->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
                <form method="POST" action="{{ route('options.destroy', [$lang, $option->id]) }}" accept-charset="UTF-8" class="btn-delete">
                  <input name="_method" type="hidden" value="DELETE">
                  <input name="_token" type="hidden" value="{{ csrf_token() }}">
                  <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
                </form>
              </td>
            </tr>
          @endforeach
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $options->links() }}

@endsection