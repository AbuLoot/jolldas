@extends('joystick.layout')

@section('head')

  @livewireStyles
  <link href="/bower_components/typeahead.js/dist/typeahead.bootstrap.css" rel="stylesheet">

@endsection

@section('content')
  <h2 class="page-header">Joytable Продукты @if (isset($category)) - {{ $category->title }} @endif</h2>

  @include('components.alerts')

  <div class="row">
    <div class="col-md-5">
      <form action="/{{ $lang }}/admin/products-search" method="get">
        <div class="input-group input-search">
          <input type="search" class="form-control input-xs typeahead-goods" name="text" placeholder="Поиск...">

          <div class="input-group-btn">
            <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Категории <span class="caret"></span></button>
            <ul class="dropdown-menu dropdown-menu-right dropdown-menu-category">
              <li><a href="/{{ $lang }}/admin/products"><b>Все продукты</b></a></li>
              <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $lang) { ?>
                <?php foreach ($nodes as $node) : ?>
                  <li><a href="/{{ $lang }}/admin/products-category/{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</a></li>
                  <?php $traverse($node->children, $prefix.'___'); ?>
                <?php endforeach; ?>
              <?php }; ?>
              <?php $traverse($categories); ?>
            </ul>
          </div>
        </div>
      </form>
    </div>

    <div class="col-md-7 text-right">
      @can('export', Auth::user())<a href="/{{ $lang }}/admin/products-export" class="btn btn-default"><i class="material-icons md-18">import_export</i> Экспорт</a>@endcan
      @can('import', Auth::user())<a href="/{{ $lang }}/admin/products-import" class="btn btn-default"><i class="material-icons md-18">import_export</i> Импорт</a>@endcan
      @can('allow-calc', Auth::user())<a href="/{{ $lang }}/admin/products-price/edit" class="btn btn-default"><i class="material-icons md-18">calculate</i> Цены</a>@endcan
      <div class="btn-group">
        <button type="button" id="submit" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Функции <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" id="actions">
          @foreach(trans('statuses.product') as $num => $status)
            <li><a data-action="{{ $num }}" href="#">Статус {{ $status['title'] }}</a></li>
          @endforeach
          <li role="separator" class="divider"></li>
          <li><a data-action="synchronize" href="#" onclick="return confirm('Изменить записи?')">Синхронизировать с учетом</a></li>
          <li role="separator" class="divider"></li>
          @foreach($modes as $mode)
            <?php $titles = unserialize($mode->title); ?>
            <li><a data-action="{{ $mode->slug }}" href="#">Режим {{ $titles[$lang]['title'] }}</a></li>
          @endforeach
          <li role="separator" class="divider"></li>
          <li><a data-action="destroy" href="#" onclick="return confirm('Удалить записи?')">Удалить</a></li>
        </ul>
      </div>
      <a href="/{{ $lang }}/admin/products/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>

    </div>
  </div>

  @livewire('joystick.joytable')

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

  @livewireScripts
  <script src="/bower_components/typeahead.js/dist/typeahead.bundle.min.js"></script>

  <script type="text/javascript">

    // Typeahead Initialization
    jQuery(document).ready(function($) {
      // Set the Options for "Bloodhound" suggestion engine
      var engine = new Bloodhound({
        remote: {
          url: '/{{ $lang }}/admin/products-search-ajax?text=%QUERY%',
          wildcard: '%QUERY%'
        },
        datumTokenizer: Bloodhound.tokenizers.whitespace('text'),
        queryTokenizer: Bloodhound.tokenizers.whitespace
      });

      $(".typeahead-goods").typeahead({
        hint: true,
        highlight: true,
        minLength: 2
      }, {
        limit: 10,
        source: engine.ttAdapter(),
        displayKey: 'title',

        templates: {
          empty: [
            '<li>&nbsp;&nbsp;&nbsp;Ничего не найдено.</li>'
          ],
          suggestion: function (data) {
            let image = (data.path == null) ? 'no-image-middle.png' : data.path + '/' + data.image;
            return '<li><a href="/{{ $lang }}/admin/products/' + data.id + '/edit"><img class="list-img" src="/img/products/' + image + '"> ' + data.title + '<br><span>Код: ' + data.barcode + '</span></a></li>'
          }
        }
      });
    });

    // submit button click
    $("#actions > li > a").click(function() {

      var action = $(this).data("action");
      var productsId = [];

      $('input[name="products_id[]"]:checked').each(function() {
        productsId.push($(this).val());
      });

      if (action == 'destroy') {
        $('#modal-progress').modal('show');
      }

      if (productsId.length > 0) {
        $.ajax({
          type: "get",
          url: '/{{ $lang }}/admin/products-actions',
          dataType: "json",
          data: {
            "action": action,
            "products_id": productsId
          },
          success: function(data) {
            console.log(data);
            location.reload();
            $('#modal-progress').modal('toggle');
          }
        });
      }
    });

    function toggleCheckbox(source) {
      var checkboxes = document.querySelectorAll('input[type="checkbox"]');
      for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source)
          checkboxes[i].checked = source.checked;
      }
    }
  </script>
@endsection