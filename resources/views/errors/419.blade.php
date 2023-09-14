@extends('errors::custom-layout')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired'))
@section('link')
	<div class="mt-3">
		<a class="btn btn-outline-primary btn-lg" href="{{ url('/login') }}">Страница входа</a>
	</div>
@endsection
