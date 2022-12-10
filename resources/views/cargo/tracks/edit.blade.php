@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/tracks" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
  </p>

  <div class="row">
    <div class="col-md-7">
      <div class="panel panel-default">
        <div class="panel-body">
          <form action="/{{ $lang }}/admin/tracks/{{ $track->id }}" method="post">
            <input type="hidden" name="_method" value="PUT">
            {!! csrf_field() !!}
            <div class="form-group">
              <label for="user_id">Пользователь</label>
              <input type="text" class="form-control" id="user_id" name="user_id" value="@if($track->user) {{ $track->user->name . ' ' . $track->user->lastname }} @endif" disabled>
            </div>
            <div class="form-group">
              <label for="code">Трек код</label>
              <input type="text" class="form-control" id="code" name="code" maxlength="80" value="{{ (old('code')) ? old('code') : $track->code }}" required>
            </div>
            <div class="form-group">
              <label for="description">Описание</label>
              <input type="text" class="form-control" id="description" name="description" maxlength="80" value="{{ (old('description')) ? old('description') : $track->description }}">
            </div>
            <div class="form-group">
              <label for="updated_at">Дата</label>
              <input type="text" class="form-control" id="updated_at" name="updated_at" maxlength="80" value="{{ (old('updated_at')) ? old('updated_at') : $track->updated_at }}" disabled>
            </div>
            <div class="form-group">
              <label for="lang">Язык</label>
              <select id="lang" name="lang" class="form-control" required>
                <option value=""></option>
                @foreach($languages as $language)
                  <option value="{{ $language->slug }}" @if($language->slug == $track->lang) selected @endif>{{ $language->title }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="status">Статус</label>
              <select id="status" name="status" class="form-control" required>
                @foreach($statuses as $status)
                  <option value="{{ $status->id }}" @if($status->id == $track->status) selected @endif>{{ $status->title }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-success"><i class="material-icons">save</i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
