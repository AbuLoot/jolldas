@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Импорт данных</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/products" class="btn btn-primary btn-sm">Назад</a>
  </p>
  <form action="/{{ $lang }}/admin/products-import" method="POST" enctype="multipart/form-data">
    {!! csrf_field() !!}
    <div class="row">
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-body">

            <div class="form-group">
              <label for="company_id">Компания</label>
              <select id="company_id" name="company_id" class="form-control">
                @foreach($companies as $company)
                  <option value="{{ $company->id }}">{{ $company->title }}</option>
                @endforeach
              </select>
            </div>

            <p><b>Проекты</b></p>
            <select name="project_id" class="form-control" size="15" required>
              <?php $traverse = function ($nodes, $prefix = null) use (&$traverse) { ?>
                <?php foreach ($nodes as $node) : ?>
                  <option value="{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                  <?php $traverse($node->children, $prefix.'___'); ?>
                <?php endforeach; ?>
              <?php }; ?>
              <?php $traverse($projects); ?>
            </select><br>

            <div class="form-group">
              <label for="file">Файл</label>
              <input type="file" id="file" name="file" accept=".xlsx, .csv, .ods" required>
              <p class="help-block">Поддерживаемые форматы файлов: xlsx, csv, ods</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group">
      <button type="submit" id="import" class="btn btn-primary">Загрузить</button>
    </div>
  </form>

  <!-- Modal Progress Bar -->
  <div class="modal fade" id="modal-progress" tabindex="-1" role="dialog" aria-labelledby="modalProgress">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content-">
        <br>
        <div class="progress">
          <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
            <span class="sr-only">100% Complete</span>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="/bower_components/typeahead.js/dist/typeahead.bundle.min.js"></script>
  <!-- Typeahead Initialization -->
  <script>
    // submit button click
    $("#import").click(function() {
      $('#modal-progress').modal('show');
    });
  </script>
@endsection
