@extends('joystick.layout')

@section('content')

  <h2 class="page-header">Трек коды
    @if(isset($_GET['text']))<small>Результаты по запросу: <b>{{ $_GET['text'] }}</b></small>@endif
  </h2>

  @include('components.alerts')

  <div class="row">
    <div class="col-md-5">
      <form action="/{{ $lang }}/admin/tracks/search/tracks" method="get">
        <div class="input-group input-search">
          <input type="search" class="form-control input-xs" name="text" placeholder="Поиск...">

          <div class="input-group-btn">
            <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
          </div>
        </div>
      </form>
      <br>
    </div>
    <div class="col-md-7">
      <p class="text-right">
        <a href="/{{ $lang }}/admin/tracks/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
      </p>
    </div>
  </div>

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
          <?php
            $activeStatus = $track->statuses->last();

            $trackAndRegion = null;

            if (in_array($activeStatus->slug, ['sorted', 'arrived', 'sent-locally', 'given']) OR in_array($activeStatus->id, [4, 5, 6, 7])) {

              $trackAndRegion = $track->regions->last()->title ?? __('statuses.regions.title');
              $trackAndRegion = '('.$trackAndRegion.', Казахстан)';
            }
          ?>
          <tr>
            <td>{{ $i++ }}</td>
            <td>
              @if($track->user)
                <a href="/{{ $lang }}/admin/tracks/user/{{ $track->user->id }}">{{ $track->user->name.' '.$track->user->lastname }}</a>
              @endif
            </td>
            <td>{{ $track->code }}</td>
            <td>{{ Str::limit($track->description, 35) }}</td>
            <td>{{ $activeStatus->pivot->created_at->format('Y-m-d') }}</td>
            <td>{{ $activeStatus->title }} {{ $trackAndRegion }}</td>
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
