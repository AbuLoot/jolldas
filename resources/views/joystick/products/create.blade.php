@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Добавление</h2>

  @include('components.alerts')

  <div class="row">
    <div class="col-md-6">

    </div>
    <div class="col-md-6">
      <p class="text-right">
        <a href="/{{ $lang }}/admin/products" class="btn btn-primary btn-sm">Назад</a>
      </p>
    </div>
  </div><br>

  <form action="{{ route('products.store', $lang) }}" method="post" enctype="multipart/form-data">
    {!! csrf_field() !!}
    <div class="row">
      <div class="col-md-7">
        <div class="panel panel-default">
          <div class="panel-heading">Основная информация</div>
          <div class="panel-body">
            <div class="form-group">
              <label for="title">Название</label>
              <input type="text" class="form-control" id="title" name="title" minlength="5" value="{{ (old('title')) ? old('title') : '' }}" required>
            </div>
            <div class="form-group">
              <label for="slug">Slug</label>
              <input type="text" class="form-control" id="slug" name="slug" minlength="2" value="{{ (old('slug')) ? old('slug') : '' }}">
            </div>
            <div class="form-group">
              <label for="sort_id">Порядковый номер</label>
              <input type="text" class="form-control" id="sort_id" name="sort_id" maxlength="5" value="{{ (old('sort_id')) ? old('sort_id') : NULL }}">
            </div>
            <div class="form-group">
              <label for="meta_title">Мета заголовок</label>
              <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="255" value="{{ (old('meta_title')) ? old('meta_title') : '' }}" required>
            </div>
            <div class="form-group">
              <label for="meta_description">Мета описание</label>
              <input type="text" class="form-control" id="meta_description" name="meta_description" maxlength="255" value="{{ (old('meta_description')) ? old('meta_description') : '' }}">
            </div>
            <div class="form-group">
              <label for="description">Описание</label>
              <textarea class="form-control" id="summernote" name="description" rows="6" maxlength="2000">{{ (old('description')) ? old('description') : '' }}</textarea>
            </div>
            <div class="form-group">
              <label for="characteristic">Характеристика</label>
              <input type="text" class="form-control" id="characteristic" name="characteristic" minlength="2" value="{{ (old('characteristic')) ? old('characteristic') : '' }}">
            </div>
            <div class="form-group">
              <label for="parameters">Параметры</label>
              <input type="text" class="form-control" id="parameters" name="parameters" minlength="5" value="{{ (old('parameters')) ? old('parameters') : '' }}">
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="barcode">Артикул</label>
                  <input type="text" class="form-control" id="barcode" name="barcode" value="{{ (old('barcode')) ? old('barcode') : NULL }}">
                </div>
                <div class="form-group">
                  <label for="count">Количество</label>
                  <input type="number" class="form-control" id="count" name="count" minlength="5" maxlength="80" value="{{ (old('count')) ? old('count') : 1 }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="price">Цена</label>
                  <div class="input-group">
                    <input type="text" class="form-control" id="price" name="price" maxlength="10" value="{{ (old('price')) ? old('price') : '' }}" required>
                    <div class="input-group-addon">{{ $currency->symbol }}</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="type">Тип</label><br>
              <label class="radio-inline">
                <input type="radio" name="type" value="1" checked> Новый
              </label>
              <label class="radio-inline">
                <input type="radio" name="type" value="2"> Б/у
              </label>
            </div>
            <div class="row" id="gallery">
              <div class="col-md-12">
                <label>Галерея</label><br>
              </div>
              @for ($i = 0; $i < 4; $i++)
                <div class="col-md-6 col-xs-12 fileinput fileinput-new" data-provides="fileinput">
                  <div class="fileinput-preview thumbnail" style="width:100%;height:200px;" data-trigger="fileinput"></div>
                  <div>
                    <span class="btn btn-default btn-sm btn-file">
                      <span class="fileinput-new"><i class="glyphicon glyphicon-folder-open"></i>&nbsp; Выбрать</span>
                      <span class="fileinput-exists"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;</span>
                      <input type="file" name="images[]" accept="image/*" multiple>
                    </span>
                    <a href="#" class="btn btn-default btn-sm fileinput-exists" data-dismiss="fileinput"><i class="glyphicon glyphicon-trash"></i> Удалить</a>
                  </div>
                </div>
              @endfor
            </div>
            <div class="form-group">
              <button type="button" class="btn btn-success" onclick="addFileinput(this);">Добавить загрузчик</button>
            </div>
            <div class="row">
              <div class="col-md-6">
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
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="status">Статус:</label>
                  <select id="status" name="status" class="form-control" required>
                    @foreach(trans('statuses.product') as $num => $status)
                      @if ($num == 1)
                        <option value="{{ $num}}" selected>{{ $status['title'] }}</option>
                      @else
                        <option value="{{ $num}}">{{ $status['title'] }}</option>
                      @endif
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">Параметры</div>
          <div class="panel-body">

            <div class="form-group">
              <label for="company_id">Компания</label>
              <select id="company_id" name="company_id" class="form-control js-basic-select">
                @foreach($companies as $company)
                  <option value="{{ $company->id }}">{{ $company->title }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <p><b>Проекты</b></p>
              <select name="project_id" class="form-control js-basic-select" size="15" required>
                <?php $traverse = function ($nodes, $prefix = null) use (&$traverse) { ?>
                  <?php foreach ($nodes as $node) : ?>
                    <option value="{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                    <?php $traverse($node->children, $prefix.'___'); ?>
                  <?php endforeach; ?>
                <?php }; ?>
                <?php $traverse($projects); ?>
              </select>
            </div>

            <p><b>Категории</b></p>
            <div class="panel panel-default">
              <div class="panel-body" style="max-height: 250px; overflow-y: auto;">
                <?php $traverse = function ($nodes, $prefix = null) use (&$traverse) { ?>
                  <?php foreach ($nodes as $node) : ?>
                    <div class="radio">
                      <label>
                        <input type="radio" name="category_id" value="{{ $node->id }}" required> {{ PHP_EOL.$prefix.' '.$node->title }}
                      </label>
                    </div>
                    <?php $traverse($node->children, $prefix.'___'); ?>
                  <?php endforeach; ?>
                <?php }; ?>
                <?php $traverse($categories); ?>
              </div>
            </div>

            <p><b>Опции</b></p>
            <div class="panel panel-default">
              <div class="panel-body" style="max-height: 250px; overflow-y: auto;">
                <?php $grouped = $options->groupBy('data'); ?>
                @forelse ($grouped as $data => $group)
                  <?php $data = json_decode($data, true); ?>
                  <p><b>{{ $data[$lang]['data'] }}</b></p>
                  @foreach ($group as $option)
                    <?php $titles = json_decode($option->title, true); ?>
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" name="options_id[]" value="{{ $option->id }}"> {{ $titles[$lang]['title'] }}
                      </label>
                    </div>
                  @endforeach
                @endforeach
              </div>
            </div>

            <p><b>Режимы</b></p>
            <div class="panel panel-default">
              <div class="panel-body" style="max-height: 150px; overflow-y: auto;">
                @foreach($modes as $mode)
                  <?php $titles = unserialize($mode->title); ?>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="modes_id[]" value="{{ $mode->id }}"> {{ $titles[$lang]['title'] }}
                    </label>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-primary">Создать</button>
    </div>
  </form>
@endsection

@section('head')
  <link href="/joystick/css/jasny-bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
@endsection

@section('scripts')
  <script src="/joystick/js/jasny-bootstrap.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
  <script src="/vendor/select2/js/select2.min.js"></script>
  <script>
    /* Summernote */
    $(document).ready(function() {
      $('#summernote').summernote({
        height: 150
      });
    });

    // In your Javascript (external .js resource or <script> tag)
    $(document).ready(function() {
      $('.js-basic-select').select2();
    });
  </script>

  <script>
    function addFileinput(i) {
      var fileinput =
        '<div class="col-md-6 fileinput fileinput-new" data-provides="fileinput">' +
            '<div class="fileinput-preview thumbnail" style="width:100%;height:200px;" data-trigger="fileinput"></div>' +
            '<div>' +
              '<span class="btn btn-default btn-sm btn-file">' +
                '<span class="fileinput-new"><i class="glyphicon glyphicon-folder-open"></i>&nbsp; Выбрать</span>' +
                '<span class="fileinput-exists"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;</span>' +
                '<input type="file" name="images[]" accept="image/*">' +
              '</span>' +
              '<a href="#" class="btn btn-default btn-sm fileinput-exists" data-dismiss="fileinput"><i class="glyphicon glyphicon-trash"></i> Удалить</a>' +
            '</div>' +
          '</div>';

      $('#gallery').append(fileinput);
    }
  </script>
@endsection
