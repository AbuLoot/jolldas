@extends('joystick.layout')

@section('content')

  <h2 class="page-header">Трек коды</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/tracks/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </p>
  <div class="table-responsive">
    <table class="table table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Пользователь</td>
          <td>Track-code</td>
          <td>Описание</td>
          <td>Дата</td>
          <td>Статус</td>
          <td>Язык</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        @foreach ($tracks as $track)
          <tr>
            <td>{{ $i++ }}</td>
            <td>@if($track->user) {{ $track->user->name.' '.$track->user->lastname }} @endif</td>
            <td>{{ $track->code }}</td>
            <td>{{ Str::limit($track->description, 35) }}</td>
            <?php $lastStatus = $track->statuses()->orderBy('created_at', 'desc')->first(); ?>
            <?php $regionTitle = $track->regions->last()->title ?? __('statuses.regions.title'); ?>
            <td>{{ $lastStatus->pivot->created_at->format('Y-m-d') }}</td>
            <td>{{ $lastStatus->title }} ({{ $regionTitle }}, Казахстан)</td>
            <td>{{ $track->lang }}</td>
            <td class="text-right">
              <a class="btn btn-link btn-xs" href="{{ route('tracks.edit', [$lang, $track->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form method="POST" action="{{ route('tracks.destroy', [$lang, $track->id]) }}" accept-charset="UTF-8" class="btn-delete">
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

  {{ $tracks->links() }}

@endsection