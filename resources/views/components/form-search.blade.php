
  <form class="col-12 col-sm-6 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto" style="position: relative;">
    <input type="search" class="form-control form-control-lg" name="text" placeholder="Поиск товаров..." aria-label="Search"
        hx-get="/{{ $lang }}/market/search-ajax"
        hx-trigger="keyup changed delay:500ms"
        hx-target="#dropdown-products">

    <div id="dropdown-products">
      <!-- List of products -->
    </div>
  </form>