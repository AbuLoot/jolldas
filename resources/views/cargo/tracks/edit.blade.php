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
              @if($track->user)
                <div class="input-group">
                  <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $track->user->name . ' ' . $track->user->lastname }}" disabled>
                  <div class="input-group-btn">
                    <a href="/{{ $lang }}/admin/tracks/{{ $track->id }}/unpin-user" class="btn btn-default"><span class="material-icons md-18" data-toggle="tooltip" data-placement="bottom" title="Открепить пользователя">close</span></a>
                  </div>
                </div>
              @else
                <div style="position: relative;">
                  <input type="text" class="form-control" id="user_id" name="text" placeholder="Поиск пользователя"
                    hx-get="/{{ $lang }}/admin/tracks/{{ $track->id }}/search/users"
                    hx-trigger="keyup changed delay:500ms"
                    hx-target="#dropdown-users">

                  <div class="input-group-items open" id="dropdown-users">
                    <!-- Users -->
                  </div>
                </div>
              @endif
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
              <?php $regionId = $track->statuses->last()->pivot->region_id; ?>
              <label for="region_id">Регионы</label>
              <select id="region_id" name="region_id" class="form-control">
                <option value=""></option>
                <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $regionId) { ?>
                  <?php foreach ($nodes as $node) : ?>
                    <option value="{{ $node->id }}" <?= ($node->id == $regionId) ? 'selected' : ''; ?>>{{ PHP_EOL.$prefix.' '.$node->title }}</option>
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

@section('scripts')
  <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
  </script>
@endsection