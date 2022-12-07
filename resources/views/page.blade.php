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

        <div>{!! $page->content !!}</div>
      </div>
    </div>

  </div>

@endsection