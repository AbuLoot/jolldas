@extends('layout')

@section('meta_title', 'Profile: '.$user->name.' '.$user->last)

@section('meta_description', 'Profile: '.$user->name.' '.$user->last)

@section('head')

@endsection

@section('content')
  <div class="container my-5">

    <div class="row">
      <div class="col-lg-5 col-md-7 col-sm-9 mx-auto">

        <div class="p-4 p-md-5 bg-light border rounded-3 bg-light">
          <h2 class="fw-bold mb-0">Мой профиль</h2>
          <br>

          <h5>{{ $user->name.' '.$user->last }}</h5>
          <p>{{ $user->email }}</p>
          <p>{{ $user->tel }}</p>

          <table class="table">
            <tbody>
              <tr>
                <th scope="col">Region</th>
                <td scope="col">{{ $user->region->title }}</td>
              </tr>
              <tr>
                <th>Address</th>
                <td>{{ $user->address }}</td>
              </tr>
              <tr>
                <th>ID client</th>
                <td>{{ $user->id_client }}</td>
              </tr>
              <tr>
                <th>ID name</th>
                <td>{{ $user->id_name }}</td>
              </tr>
            </tbody>
          </table>
          <a href="/{{ $lang }}/profile/edit" class="btn btn-primary btn-lg">Изменить</a>

        </div>
      </div>
    </div>
  </div>
@endsection