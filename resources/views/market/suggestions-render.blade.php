<ul class="suggestions__list">
  @foreach ($products->take(20) as $product)
    <li class="suggestions__item">
      <a class="suggestions__item-link" href="/p/{{ $product->id.'-'.$product->slug }}">
        <div class="suggestions__item-info">
          <div class="suggestions__item-name">{{ $product->title }}</div>
          <div class="suggestions__item-meta">КОД: {{ $product->barcode }}, Цена: {{ $product->price }}〒</div>
        </div>
        <div class="suggestions__item-price"></div>
      </a>
    </li>
  @endforeach
</ul>