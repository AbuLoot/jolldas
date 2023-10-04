@if($products->count() > 0)
  <div class="dropdown-menu d-block pt-0 w-100 shadow overflow-hidden" style="position: absolute;">
    <ul class="list-unstyled mb-0">
      @foreach($products as $product)
        <li>
          <a href="/{{ $lang }}/market/{{ $product->id.'-'.$product->slug }}" class="dropdown-item d-flex align-items-center gap-2 py-2">{{ $product->title }}</a>
        </li>
      @endforeach
    </ul>
  </div>
@endif