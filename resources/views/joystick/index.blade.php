@extends('joystick.layout')

@section('content')

  @include('components.alerts')

  <br>
  <div>
    <div class="col-md-4">
      <div class="well text-center">
        <h3>Количество<br> заявок</h3>
        <h2>{{ $count_apps }}</h2>
      </div> 
    </div>
    <div class="col-md-4">
      <div class="well text-center">
        <h3>Количество<br> пользователей</h3>
        <h2>{{ $count_users }}</h2>
      </div> 
    </div>
    <div class="col-md-4">
      <div class="well text-center">
        <h3>Количество<br> новостей</h3>
        <h2>{{ $count_posts }}</h2>
      </div> 
    </div>

    <img src="/file-manager/bg-joystick-2.png" class="img-responsive center-block">
  </div>

@endsection