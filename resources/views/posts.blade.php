@extends('layout')

@section('meta_title', (!empty($page->meta_title)) ? $page->meta_title : $page->title)

@section('meta_description', (!empty($page->meta_description)) ? $page->meta_description : $page->title)

@section('head')

@endsection

@section('content')

  <div class="container my-5">
  
    <div class="row">

      <div class="col-8">
        <h1>{{ $page->title }}</h1>

        @foreach($posts as $post)
          <div class="card mb-3">
            <div class="row g-0">
              <div class="col-md-4">
                @if($post->image)
                  <img src="/img/posts/{{ $post->image }}" class="img-fluid rounded-start" alt="{{ $post->title }}">
                @else
                  <img src="/img/no-image-middle.png" class="img-fluid rounded-start" alt="{{ $post->title }}">
                @endif
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h5 class="card-title">
                    <a href="/i/news/{{ $post->slug }}">{{ $post->title }}</a>
                  </h5>
                  <p class="card-text">{!! strip_tags(Str::limit($post->content, 260)) !!}</p>
                  <p class="card-text"><small class="text-muted">{{ $post->getDateAttribute() }}</small></p>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <nav aria-label="Page navigation example">
      {{ $posts->links() }}
    </nav>
  </div>

@endsection