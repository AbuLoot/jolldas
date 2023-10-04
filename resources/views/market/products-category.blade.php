@extends('layout')

@section('meta_title', (!empty($category->meta_title)) ? $category->meta_title : $category->title)

@section('meta_description', (!empty($category->meta_description)) ? $category->meta_description : $category->title)

@section('head')
  <script src="https://unpkg.com/htmx.org@1.9.6"></script>
@endsection

@section('content')

  <div class="py-3 border-bottom mb-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <h4 class="col-12 col-lg-4 mb-md-2 mb-lg-0">{{ $category->title }}</h4>

      @include('components.form-search')
    </div>
  </div>

  <div class="container">
    <div class="row g-3">
      <div class="col-12 col-sm-12 col-md-12 col-lg-3">
        <div class="list-group d-none d-md-none d-lg-block">
          <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $lang) { ?>
            <?php foreach ($nodes as $node) : ?>
              <a href="/{{ $lang }}/market/{{ $node->slug.'/'.$node->id }}" class="list-group-item list-group-item-action">{{ $node->title }}</a>
              <?php $traverse($node->children, $prefix.'___'); ?>
            <?php endforeach; ?>
          <?php }; ?>
          <?php $traverse($categories); ?>
        </div>


        <div class="dropdown d-block d-md-block d-lg-none">
          <div class="d-grid gap-2">
            <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Категории</button>
            <ul class="dropdown-menu dropdown-menu-end" style="display: static;">
              <li><a class="dropdown-item" href="/{{ $lang }}/market">Все</a></li>
              <?php $traverseM = function ($nodes, $prefix = null) use (&$traverseM, $lang) { ?>
                <?php foreach ($nodes as $node) : ?>
                  <li><a class="dropdown-item" href="/{{ $lang }}/market/{{ $node->slug.'/'.$node->id }}">{{ $node->title }}</a></li>
                  <?php $traverseM($node->children, $prefix.'___'); ?>
                <?php endforeach; ?>
              <?php }; ?>
              <?php $traverseM($categories); ?>
            </ul>
          </div>
        </div>

      </div>
      <div class="col-12 col-sm-12 col-md-12 col-lg-9">
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-3 row-cols-lg-3 g-1 gy-2 g-md-3">
          @foreach($products as $product)
            <div class="col">
            <div class="card shadow-sm">
              <a href="/{{ $lang }}/market/{{ $product->id.'-'.$product->slug }}">
                <img src="/img/products/{{ $product->path.'/'.$product->image }}" class="card-img-top" alt="{{ $product->title }}">
              </a>
              <div class="card-body">
                <p class="card-text"><a href="/{{ $lang }}/market/{{ $product->id.'-'.$product->slug }}">{{ $product->title }}</a></p>
                <div class="d-flex justify-content-between align-items-center">
<!--                   <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary">To cart</button>
                  </div> -->
                  <small class="text-body-secondary">{{ $product->price }}〒</small>
                </div>
              </div>
            </div>
            </div>
          @endforeach
        </div>

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
          $('*[data-product-id="'+productId+'"]').replaceWith('<a href="/cart" class="btn btn-dark" data-toggle="tooltip" data-placement="top" title="Перейти в корзину">Оформить</a>');
          $('#count-items-m').text(data.countItems);
          $('#count-items').text(data.countItems);
          alert('Товар добавлен в корзину');
        }
      });
    }
  </script>
@endsection