@extends('layout')

@section('meta_title', (!empty($page->meta_title)) ? $page->meta_title : $page->title)

@section('meta_description', (!empty($page->meta_description)) ? $page->meta_description : $page->title)

@section('head')

@endsection

@section('content')

  <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="img/jolldas-2.jpg" class="d-block w-100 h-100" alt="...">
        <div class="carousel-caption d-none-d-md-block">
          <h1 class="d-none d-md-block fw-normal shadow-1">Отслеживание по трек коду</h1>
          <form action="/search-track" method="get" class="col-12 col-lg-8 offset-lg-2 mt-5 mt-lg-0 mb-3 mb-lg-0 me-lg-2 py-2" role="search">
            <input type="search" name="code" class="form-control form-control-dark form-control-lg text-bg-dark" placeholder="Введите трек код..." aria-label="Search" min="4" required>
          </form>
        </div>
      </div>
    </div>
  </div>

  @if($posts->isNotEmpty())
    <div class="container my-3 my-lg-5">
      <div class="row gx-2 gy-2">
        @foreach($posts as $post)
          <div class="col">
            <div class="card shadow-sm">
              @if($post->image)
                <img src="/img/posts/{{ $post->image }}" class="card-img-top" alt="{{ $post->title }}">
              @endif

              <div class="card-body">
                <h5 class="card-title">{{ $post->title }}</h5>
                <p class="card-text">{!! Str::limit($post->content, 50) !!}</p>
                <a href="/i/news/{{ $post->slug }}" class="btn btn-link">Дальше</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- START THE FEATURETTES -->
  <div class="container">
    <br>
    @if(!empty($promo))
      {!! $promo->content !!}
    @endif

  </div>

@endsection

@section('scripts')

@endsection