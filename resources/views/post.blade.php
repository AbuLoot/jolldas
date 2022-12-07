@extends('layout')

@section('meta_title', (!empty($post->meta_title)) ? $post->meta_title : $post->title)

@section('meta_description', (!empty($post->meta_description)) ? $post->meta_description : $post->title)

@section('head')

@endsection

@section('content')

  <div class="container my-5">
  
    <div class="row">

      <div class="col-8">
        @if($post->image)
          <img src="/img/posts/{{ $post->image }}" class="img-fluid" alt="{{ $post->title }}">
        @endif

        <h1>{{ $post->title }}</h1>
        <p>Дата: {{ $post->getDateAttribute() }}</p>

        <div>{!! $post->content !!}</div>
      </div>
    </div>

  </div>

@endsection