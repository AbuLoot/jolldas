@extends('layout')

@section('meta_title', $product->meta_title ?? $product->title.' - '.$category->title)
@section('meta_description', $product->meta_description ?? $product->title.' - '.$category->title)

@section('head')
  <!-- <link rel="stylesheet" href="/vendor/photoswipe/photoswipe.css"> -->
  <!-- <link rel="stylesheet" href="/vendor/photoswipe/default-skin/default-skin.css"> -->
@endsection

@section('content')

  <?php $items = session('items'); ?>

  <div class="py-3 border-bottom mb-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <h4 class="col-12 col-lg-4 mb-md-2 mb-lg-0">Market</h4>

      <form class="col-10 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto">
        <input wire:model="search" type="search" class="form-control form-control-lg" placeholder="Enter track code..." aria-label="Search">
      </form>
    </div>
  </div>

  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/{{ $lang }}/market">Главная</a></li>
        <li class="breadcrumb-item"><a href="/{{ $lang }}/market/{{ $product->category->slug.'/'.$product->category->id }}">{{ $product->category->title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $product->title }}</li>
      </ol>
    </nav>
    <div class="row g-3">
      <div class="col-6">
        <div id="carousel" class="carousel slide">
          <div class="carousel-inner">
            <?php $images = unserialize($product->images); ?>
            @if(!empty($images))
              <?php $firstItem = [0 => 'active']; ?>
              @foreach ($images as $k => $image)
                <div class="carousel-item {{ $firstItem[$k] ?? null }}">
                  <img src="/img/products/{{ $product->path.'/'.$images[$k]['image'] }}" class="d-block w-100" alt="{{ $product->title }}">
                </div>
              @endforeach
            @else
              <div class="carousel-item active">
                <img src="/img/products/{{ $product->image }}" class="d-block w-100" alt="{{ $product->title }}">
              </div>
            @endif
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>

      <div class="col-6">
        <h1>{{ $product->title }}</h1>
        @if(isset($product->company)) <div>Марка: {{ $product->company->title }}</div>@endif
        <div>Артикулы:
          <br>
          <?php $barcodes = json_decode($product->barcodes, true) ?? []; ?>
          @foreach($barcodes as $barcode)
           {{ $barcode }}<br>
          @endforeach
        </div>

        <div>Товар: {{ trans('statuses.types.'.$product->type) }}</div>
        <div>Количество: {{ $product->count_web }}шт</div>
        <h4>{{ $product->price }}₸</h4>

        <p>{!! $product->description !!}</p>

        <div>
          @foreach($product->modes as $mode)
            <?php $titles = unserialize($mode->title); ?>
            <span class="btn-xs product-card__badge--<?= (in_array($mode->slug, ['new', 'sale', 'hot'])) ? $mode->slug : 'default'; ?>">{{ $titles[$lang]['title'] }}</span>
          @endforeach
        </div>
        <br>

      </div>
    </div>
  </div>

@endsection

@section('scripts')
  <!-- <script src="/vendor/photoswipe/photoswipe.min.js"></script> -->
  <!-- <script src="/vendor/photoswipe/photoswipe-ui-default.min.js"></script> -->

  <script>
    const carousel = new bootstrap.Carousel('#carousel')

    // Add to cart
    function addToCart(i) {
      var productId = $(i).data("product-id");

      $.ajax({
        type: "get",
        url: '/add-to-cart/'+productId,
        dataType: "json",
        data: {},
        success: function(data) {
          $('*[data-product-id="'+productId+'"]').replaceWith('<a href="/cart" class="btn btn-dark btn-lg" data-toggle="tooltip" data-placement="top" title="Перейти в корзину">Оформить</a>');
          $('#count-items-m').text(data.countItems);
          $('#count-items').text(data.countItems);
          alert('Товар добавлен в корзину');
        }
      });
    }
  </script>
  @endsection