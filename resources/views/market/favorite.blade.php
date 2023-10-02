@extends('layout')

@section('meta_title', 'Избранные')

@section('meta_description', 'Избранные')

@section('head')

@endsection

@section('content')

  <?php $items = session('items'); ?>
  <?php $favorite = session('favorite'); ?>

  <div class="page-header">
    <div class="page-header__container container">
      <br>
      <div class="page-header__title">
        <h1>Избранные</h1>
      </div>
    </div>
  </div>
  <div class="block">
    <div class="container">
      @if ($products->count() > 0)
        <table class="wishlist">
          <thead class="wishlist__head">
            <tr class="wishlist__row">
              <th class="wishlist__column wishlist__column--image">Картинка</th>
              <th class="wishlist__column wishlist__column--product">Продукт</th>
              <th class="wishlist__column wishlist__column--stock">Статус</th>
              <th class="wishlist__column wishlist__column--price">Цена</th>
              <th class="wishlist__column wishlist__column--tocart"></th>
              <th class="wishlist__column wishlist__column--remove"></th>
            </tr>
          </thead>
          <tbody class="wishlist__body">
            @foreach($products as $product)
              <tr class="wishlist__row">
                <td class="wishlist__column wishlist__column--image">
                  <a class="product-image__body" href="/p/{{ $product->id.'-'.$product->slug }}"><img class="product-image__img" src="/img/products/{{ $product->path.'/'.$product->image }}"></a>
                </td>
                <td class="wishlist__column wishlist__column--product">
                  <a href="/p/{{ $product->id.'-'.$product->slug }}" class="wishlist__product-name">{{ $product->title }}</a>
                </td>
                <td class="wishlist__column wishlist__column--stock">
                  <div class="badge badge-success">На складе</div>
                </td>
                <td class="wishlist__column wishlist__column--price text-nowrap">{{ $product->price }}〒</td>
                <td class="wishlist__column wishlist__column--tocart">
                  @if ($product->count_web == 0)
                    <button class="btn btn-primary" type="button" data-product-id="{{ $product->id }}" onclick="preOrder(this);">Предзаказ</button>
                  @elseif (is_array($items) AND isset($items['products_id'][$product->id]))
                    <a href="/cart" class="btn btn-dark" data-toggle="tooltip" data-placement="top" title="Перейти в корзину">Оформить</a>
                  @else
                    <button class="btn btn-primary" type="button" data-product-id="{{ $product->id }}" onclick="addToCart(this);" title="Добавить в корзину">В корзину</button>
                  @endif
                </td>
                <td class="wishlist__column wishlist__column--remove">
                  <button class="btn btn-light d-none-d-sm-block <?php if (is_array($favorite) AND in_array($product->id, $favorite['products_id'])) echo 'btn-liked'; ?>" type="button" data-favourite-id="{{ $product->id }}" onclick="toggleFavourite(this);">
                    <svg width="18px" height="18px"><use xlink:href="/img/sprite.svg#wishlist-16"></use></svg>
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <h2>Избранных нет</h2>
        <p><a href="/" class="btn btn-primary btn-lg">Перейти к выбору</a></p>
      @endif
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
          $('*[data-product-id="'+productId+'"]').replaceWith('<a href="/cart" class="btn btn-dark" data-toggle="tooltip" data-placement="top" title="Перейти в корзину">Оформить</a>');
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