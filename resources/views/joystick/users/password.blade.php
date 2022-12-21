@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/users" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
  </p>

  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">Основная информация</div>
        <div class="panel-body">
          <form method="POST" action="/{{ $lang }}/admin/users/password/{{ $user->id }}">
            <input type="hidden" name="_method" value="PUT">
            @csrf

            <div class="form-group">
              <label for="email">{{ __('E-Mail') }}:</label>
              <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>

              @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('email') }}</strong>
                </span>
              @endif
            </div>

            <div class="form-group">
              <label for="password">{{ __('Password') }}:</label>
              <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

              @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('password') }}</strong>
                </span>
              @endif
            </div>

            <div class="form-group">
              <label for="password-confirm">{{ __('Confirm Password') }}:</label>
              <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('Reset Password') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

@endsection
