@extends('joystick.layout')

@section('content')
  <h2 class="page-header"><span class="text-uppercase">Joytable</span> Продукты @if (isset($category)) - {{ $category->title }} @endif</h2>

  @include('components.alerts')

  <div class="row">
    <div class="col-md-6">
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
      </form><br>
    </div>

    <div class="col-md-6 text-right">
      @can('export', Auth::user())<a href="/{{ $lang }}/admin/products-export" class="btn btn-default"><i class="material-icons md-18">import_export</i> Экспорт</a>@endcan
      @can('import', Auth::user())<a href="/{{ $lang }}/admin/products-import" class="btn btn-default"><i class="material-icons md-18">import_export</i> Импорт</a>@endcan
      @can('allow-calc', Auth::user())<a href="/{{ $lang }}/admin/products-price/edit" class="btn btn-default"><i class="material-icons md-18">calculate</i> Цены</a>@endcan
      <a href="/{{ $lang }}/admin/products/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
    </div><br>
  </div>

  <div class="table-responsive table-products">
    <table class="table data table-striped table-condensed table-hover">
      <thead>
        <tr class="active">
          <td class="text-right hidden-xs">Функции</td>
          <td>Картинка</td>
          <td>Название</td>
          <td>Цена</td>
          <td>Количество</td>
          <td>Категории</td>
          <td>Артикул</td>
          <td>Статус</td>
        </tr>
      </thead>
      <tbody>
        @foreach ($products as $product)
          <tr data-id="{{ $product->id }}">
            <td class="text-right text-nowrap hidden-xs">
              <form class="btn-delete" method="POST" action="{{ route('products.destroy', [$lang, $product->id]) }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
              </form>
              <div id="edit" class="btn btn-link btn-xs"><i class="material-icons md-18">mode_edit</i></div>
              <div id="save" class="btn btn-link btn-xs"><i class="material-icons md-18">save</i></div>
            </td>
            <td><img src="/img/products/{{ $product->path.'/'.$product->image }}" class="img-responsive" style="width:80px;height:auto;"></td>
            <td class="cell-size data" data-column="title">{{ $product->title }}</td>
            <td class="data" data-column="price">{{ $product->price }}</td>
            <td class="data" data-column="count">{{ $product->count }}</td>
            <td class="data-select text-nowrap">{{ $product->category->title }}</td>
            <td>{{ $product->barcode }}</td>
            <td class="text-{{ trans('statuses.product.'.$product->status.'.style') }}">{{ trans('statuses.product.'.$product->status.'.title') }}</td>
            <th class="fix-col">
              <div id="edit" class="btn btn-link btn-xs btn-fix-col"><i class="material-icons md-18">mode_edit</i></div>
              <div id="save" class="btn btn-link btn-xs btn-fix-col"><i class="material-icons md-18">save</i></div>
              <form class="btn-delete btn-fix-col" method="POST" action="{{ route('products.destroy', [$lang, $product->id]) }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
              </form>
            </th>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $products->links() }}

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
<?php 

  $arr = json_encode($categories->pluck( 'title', 'id')->toArray());

 ?>
@endsection

@section('head')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="/bower_components/typeahead.js/dist/typeahead.bootstrap.css" rel="stylesheet">
@endsection

@section('scripts')
  <script src="/bower_components/typeahead.js/dist/typeahead.bundle.min.js"></script>
  <script>
    $(document).on('click', '#edit', function() {
      $(this).parent().siblings('td.data').each(function() {
        var content = $(this).html();
        $(this).html('<input id="joytable" class="form-control" value="' + content + '" />');
      });

      $(this).siblings('#save').css('display','inline-block');
      $(this).siblings('#cancel').hide();
      $(this).hide();
    });

    $(document).on('click', '#save', function() {
      var product = new Array;
      product['id'] = $(this).parent().parent().data('id');

      $('input#joytable').each(function() {
        var content = $(this).val();
        product[$(this).parent().data('column')] = content;

        $(this).html(content);
        $(this).contents().unwrap();
      });

      $.ajax({
        type: "post",
        url: '/{{ $lang }}/admin/joytable-update',
        dataType: "json",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          'id': product.id,
          'title': product.title,
          'price': product.price,
          'count': product.count
        },
        success: function(data) {
          console.log(data);
        }
      });

      $(this).siblings('#edit').show();
      $(this).siblings('#cancel').show();
      $(this).hide();      
    });

    $(document).on('click', '#cancel', function() {
      $(this).contents().unwrap();
    });

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

  </script>
@endsection
