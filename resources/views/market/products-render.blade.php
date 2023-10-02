<?php $items = session('items'); ?>
<?php $favorite = session('favorite'); ?>

<div class="row row-custom">
  @foreach ($products as $product)
    <div class="col-md-4 col-6">
      <div class="product-card">
        @if(isset($product->modes))
          <div class="product-card__badges-list">
            @foreach($product->modes as $mode)
              <?php $titles = unserialize($mode->title); ?>
              <div class="product-card__badge product-card__badge--<?= (in_array($mode->slug, ['new', 'sale', 'hot'])) ? $mode->slug : 'default'; ?>">{{ $titles[app()->getLocale()]['title'] }}</div>
            @endforeach
          </div>
        @endif
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
              <a href="/i/contacts" class="btn btn-dark">Предзаказ</a>
            @elseif (is_array($items) AND isset($items['products_id'][$product->id]))
              <a href="/cart" class="btn btn-dark" data-toggle="tooltip" data-placement="top" title="Перейти в корзину">Оформить</a>
            @else
              <button class="btn btn-primary text-nowrap" type="button" data-product-id="{{ $product->id }}" onclick="addToCart(this);" title="Добавить в корзину">В корзину</button>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div><br>

<!-- Pagination -->
{{ $products->links() }}
