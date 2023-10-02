@extends('layout')

@section('meta_title', 'Поиск')

@section('meta_description', 'Поиск')

@section('head')

@endsection

@section('content')

  <?php $items = session('items'); ?>
  <?php $favorite = session('favorite'); ?>

  <br>
  <div class="page-header__container container">
    <div class="page-header__title">
      <h1>Результат по запросу <b>"{{ $text }}"</b></h1>
    </div>
  </div>
  <!-- Products 1 -->
  <div class="container">
    <div class="row row-custom">
      @foreach ($products as $product)
        <div class="col-md-3 col-6">
          <div class="product-card">
            <div class="product-card__badges-list">
              @foreach($product->modes as $mode)
                <?php $titles = unserialize($mode->title); ?>
                <div class="product-card__badge product-card__badge--<?= (in_array($mode->slug, ['new', 'sale', 'hot'])) ? $mode->slug : 'default'; ?>">{{ $titles[$lang]['title'] }}</div>
              @endforeach
            </div>
            <a href="/p/{{ $product->id.'-'.$product->slug }}" class="product-image__body">
              <img class="product-image__img" src="/img/products/{{ $product->path.'/'.$product->image }}" alt="{{ $product->title }}">
            </a>
            <div class="product-card__info">
              <div class="product-card__name">
                <a href="/p/{{ $product->id.'-'.$product->slug }}">{{ $product->title }}</a>
              </div>
              <div class="product-card__prices">{{ $product->price }}〒</div>
              <div class="product-card__buttons">
                @if ($product->count_web == 0)
                  <button class="btn btn-dark" type="button" data-product-id="{{ $product->id }}" onclick="preOrder(this);">Предзаказ</button>
                @elseif  (is_array($items) AND isset($items['products_id'][$product->id]))
                  <a href="/cart" class="btn btn-dark" data-toggle="tooltip" data-placement="top" title="Перейти в корзину">Оплатить</a>
                @else
                  <button class="btn btn-primary text-nowrap" type="button" data-product-id="{{ $product->id }}" onclick="addToCart(this);" title="Добавить в корзину">В корзину</button>
                @endif
              </div>
            </div>
          </div>
        </div>
      @endforeach

      <!-- Pagination -->
      <div class="col-12">
        <br>
        {{ $products->links() }}
      </div>
    </div>
  </div>

@endsection


@section('scripts')
  <script>
    // Add to cart
    function addToCart(i) {
      var productId = $(i).data("product-id");

      $.ajax({
        type: "get",
        url: '/add-to-cart/'+productId,
        dataType: "json",
        data: {},
        success: function(data) {
          $('*[data-product-id="'+productId+'"]').replaceWith('<a href="/cart" class="btn btn-dark" data-toggle="tooltip" data-placement="top" title="Перейти в корзину">Оплатить</a>');
          $('#count-items-m').text(data.countItems);
          $('#count-items').text(data.countItems);
          alert('Товар добавлен в корзину');
        }
      });
    }

    // Toggle favourite
    function toggleFavourite (f) {
      var productId = $(f).data("favourite-id");

      $.ajax({
        type: "get",
        url: '/toggle-favourite/'+productId,
        dataType: "json",
        data: {},
        success: function(data) {
          if (data.status == true) {
            $('*[data-favourite-id="'+productId+'"]').addClass('btn-liked');
          } else {
            $('*[data-favourite-id="'+productId+'"]').removeClass('btn-liked');
          }
          $('#count-favorite-m').text(data.countFavorite);
          $('#count-favorite').text(data.countFavorite);
        }
      });
    }
  </script>
@endsection