@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')

  <div class="row">
    <div class="col-md-6">
      <ul class="nav nav-tabs">
        @foreach ($languages as $language)
          <li role="presentation" @if ($language->slug == $lang) class="active" @endif><a href="/{{ $language->slug }}/admin/options/{{ $option->id }}/edit">{{ $language->title }}</a></li>
        @endforeach
      </ul>
    </div>
    <div class="col-md-6">
      <p class="text-right">
        <a href="/{{ $lang }}/admin/options" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
      </p>
    </div>
  </div><br>

  <div class="panel panel-default">
    <div class="panel-body">
      <form action="/{{ $lang }}/admin/options/{{ $option->id }}" method="post">
        <input type="hidden" name="_method" value="PUT">
        {!! csrf_field() !!}

        <div class="form-group">
          <label for="title">Название</label>
          <?php $titles = json_decode($option->title, true); ?>
          <input type="text" class="form-control" id="title" name="title" maxlength="80" value="{{ (old('title')) ? old('title') : $titles[$lang]['title'] }}">
        </div>
        <div class="form-group">
          <label for="slug">Slug</label>
          <input type="text" class="form-control" id="slug" name="slug" maxlength="80" value="{{ (old('slug')) ? old('slug') : $option->slug }}">
        </div>
        <div class="form-group">
          <label for="sort_id">Номер</label>
          <input type="text" class="form-control" id="sort_id" name="sort_id" value="{{ (old('sort_id')) ? old('sort_id') : $option->sort_id }}">
        </div>
        <div class="form-group">
          <label for="data">Группа</label>
          <?php $data = json_decode($option->data, true); ?>
          <input type="text" class="form-control" id="data" name="data" value="{{ (old('data')) ? old('data') : $data[$lang]['data'] }}">
        </div>
        <div class="form-group">
          <label for="lang">Язык</label>
          <select id="lang" name="lang" class="form-control" required>
            <option value=""></option>
            @foreach($languages as $language)
              @if ($language->slug == $lang)
                <option value="{{ $language->slug }}" selected>{{ $language->title }}</option>
              @else
                <option value="{{ $language->slug }}">{{ $language->title }}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-success"><i class="material-icons">save</i></button>
        </div>
      </form>
    </div>
  </div>
@endsection
