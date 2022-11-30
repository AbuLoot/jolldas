@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Добавление</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/categories" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
  </p>
  <div class="panel panel-default">
    <div class="panel-body">
      <form action="{{ route('categories.store', $lang) }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <div class="form-group">
          <label for="title">Название</label>
          <input type="text" class="form-control" id="title" name="title" minlength="2" maxlength="80" value="{{ (old('title')) ? old('title') : '' }}" required>
        </div>
        <div class="form-group">
          <label for="title_extra">Название дополнительное</label>
          <input type="text" class="form-control" id="title_extra" name="title_extra" minlength="2" maxlength="80" value="{{ (old('title_extra')) ? old('title_extra') : '' }}">
        </div>
        <div class="form-group">
          <label for="slug">Slug</label>
          <input type="text" class="form-control" id="slug" name="slug" minlength="2" maxlength="80" value="{{ (old('slug')) ? old('slug') : '' }}">
        </div>
        <div class="form-group">
          <label for="category_id">Категории</label>
          <select id="category_id" name="category_id" class="form-control">
            <option value=""></option>
            <?php $traverse = function ($nodes, $prefix = null) use (&$traverse) { ?>
              <?php foreach ($nodes as $node) : ?>
                <option value="{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                <?php $traverse($node->children, $prefix.'___'); ?>
              <?php endforeach; ?>
            <?php }; ?>
            <?php $traverse($categories); ?>
          </select>
        </div>
        <div class="form-group">
          <label for="image">Картинка</label>
          <div class="input-group">
            <span class="input-group-btn">
              <button class="btn btn-default" type="button" data-toggle="modal" data-target="#filemanager"><i class="material-icons md-18">folder</i> Выбрать</button>
            </span>
            <input type="text" class="form-control" id="image" name="image" value="{{ (old('image')) ? old('image') : '' }}">
          </div>
          <!-- Filemanager -->
          <div class="modal fade" id="filemanager" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Файловый менеджер</h4>
                </div>
                <div class="modal-body">
                  <iframe src="<?= url($lang.'/admin/filemanager'); ?>" frameborder="0" style="width:100%;min-height:600px"></iframe>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="sort_id">Номер</label>
          <input type="text" class="form-control" id="sort_id" name="sort_id" maxlength="5" value="{{ (old('sort_id')) ? old('sort_id') : 0 }}">
        </div>
        <div class="form-group">
          <label for="meta_title">Мета заголовок</label>
          <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="255" value="{{ (old('meta_title')) ? old('meta_title') : '' }}">
        </div>
        <div class="form-group">
          <label for="meta_description">Мета описание</label>
          <input type="text" class="form-control" id="meta_description" name="meta_description" maxlength="255" value="{{ (old('meta_description')) ? old('meta_description') : '' }}">
        </div>
        <div class="form-group">
          <label for="lang">Язык</label>
          <select id="lang" name="lang" class="form-control" required>
            @foreach($languages as $language)
              @if (old('lang') == $language->slug)
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
              <input type="radio" id="status" name="status" value="{{ $num }}" @if($num == 1) checked @endif> {{ $status['title'] }}
            </label>
          @endforeach
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-success"><i class="material-icons">save</i></button>
        </div>
      </form>
    </div>
  </div>
@endsection
