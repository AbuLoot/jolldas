@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Добавление</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/tracks" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
  </p>

  <div class="row">
    <div class="col-md-7">
      <div class="panel panel-default">
        <div class="panel-body">
          <form action="{{ route('tracks.store', $lang) }}" method="post">
            {!! csrf_field() !!}
            <div class="form-group">
              <label for="user_id">Пользователь</label>
              <input type="text" class="form-control" id="user_id" name="user_id" value="{{ (old('user_id')) ? old('user_id') : NULL }}">
            </div>
            <div class="form-group">
              <label for="code">Трек код</label>
              <input type="text" class="form-control" id="code" name="code" maxlength="80" value="{{ (old('code')) ? old('code') : '' }}" required>
            </div>
            <div class="form-group">
              <label for="description">Описание</label>
              <input type="text" class="form-control" id="description" name="description" maxlength="80" value="{{ (old('description')) ? old('description') : '' }}">
            </div>
            <div class="form-group">
              <label for="lang">Язык</label>
              <select id="lang" name="lang" class="form-control" required>
                @foreach($languages as $language)
                  <option value="{{ $language->slug }}" @if($language->slug == $lang) selected @endif>{{ $language->title }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="status">Статус</label>
              <select id="status" name="status" class="form-control" required>
                @foreach($statuses as $status)
                  <option value="{{ $status->id }}" @if(old('status') == $status->id) selected @endif>{{ $status->title }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="region_id">Регионы</label>
              <select id="region_id" name="region_id" class="form-control">
                <option value=""></option>
                <?php $traverse = function ($nodes, $prefix = null) use (&$traverse) { ?>
                  <?php foreach ($nodes as $node) : ?>
                    <option value="{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                    <?php $traverse($node->children, $prefix.'___'); ?>
                  <?php endforeach; ?>
                <?php }; ?>
                <?php $traverse($regions); ?>
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
