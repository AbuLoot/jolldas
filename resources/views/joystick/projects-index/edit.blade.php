@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/projects-index" class="btn btn-primary btn-sm">Назад</a>
  </p>
  <div class="panel panel-default">
    <div class="panel-body">
      <form action="{{ route('projects-index.update', [$lang, $project_index->id]) }}" method="post" enctype="multipart/form-data">
        <input name="_method" type="hidden" value="PUT">
        {!! csrf_field() !!}
        <div class="form-group">
          <label for="sort_id">Номер</label>
          <input type="text" class="form-control" id="sort_id" name="sort_id" maxlength="5" value="{{ (old('sort_id')) ? old('sort_id') : $project_index->sort_id }}">
        </div>
        <div class="form-group">
          <label for="title">Название</label>
          <input type="text" class="form-control" id="title" name="title" minlength="2" maxlength="80" value="{{ (old('title')) ? old('title') : $project_index->title }}" required>
        </div>
        <div class="form-group">
          <label for="original">Оригинал</label>
          <input type="text" class="form-control" id="original" name="original" minlength="2" maxlength="80" value="{{ (old('original')) ? old('original') : $project_index->original }}">
        </div>
        <div class="form-group">
          <label for="lang">Язык</label>
          <select id="lang" name="lang" class="form-control" required>
            <option value=""></option>
            @foreach($languages as $language)
              @if ($project_index->lang == $language->slug)
                <option value="{{ $language->slug }}" selected>{{ $language->title }}</option>
              @else
                <option value="{{ $language->slug }}">{{ $language->title }}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="status">Статус</label>
          @foreach(trans('statuses.data') as $num => $status)
            <br>
            <label>
              <input type="radio" id="status" name="status" value="{{ $num }}" @if ($num == $project_index->status) checked @endif> {{ $status['title'] }}
            </label>
          @endforeach
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Обновить</button>
        </div>
      </form>
    </div>
  </div>
@endsection
