@extends('layout')

@section('meta_title', (!empty($category->meta_title)) ? $category->meta_title : $category->title)

@section('meta_description', (!empty($category->meta_description)) ? $category->meta_description : $category->title)

@section('head')

@endsection

@section('content')

  <div class="py-3 border-bottom mb-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <h4 class="col-12 col-lg-4 mb-md-2 mb-lg-0">{{ $category->title }}</h4>

      <form class="col-10 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto">
        <input wire:model="search" type="search" class="form-control form-control-lg" placeholder="Enter track code..." aria-label="Search">
      </form>
    </div>
  </div>

  <div class="container">
    <div class="row g-3">
      <div class="col-12 col-sm-12 col-md-12 col-lg-3">
        <div class="list-group">
          <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $lang) { ?>
            <?php foreach ($nodes as $node) : ?>
              <a href="/{{ $lang }}/market/{{ $node->slug.'/'.$node->id }}" class="list-group-item list-group-item-action">{{ $node->title }}</a>
              <?php $traverse($node->children, $prefix.'___'); ?>
            <?php endforeach; ?>
          <?php }; ?>
          <?php $traverse($categories); ?>
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
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary">To cart</button>
                  </div>
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