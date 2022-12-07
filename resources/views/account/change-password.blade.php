<x-app-layout>

  <div class="row">
    <div class="col-lg-5 col-md-7 col-sm-9 mx-auto">

      @include('components.alerts')

      <form action="/{{ $lang }}/profile/password" method="post" class="p-4 p-md-5 bg-light border rounded-3 bg-light">
        <input type="hidden" name="_method" value="PUT">
        {!! csrf_field() !!}
        <h2 class="fw-bold mb-0">Изменение пароля</h2>
        <br>

        <div class="form-floating mb-3">
          <input type="email" class="form-control rounded-3" name="email" id="email" value="{{ old('email') }}" placeholder="name@example.com" required>
          <label for="email">Email адрес</label>
        </div>
        <div class="form-floating mb-3">
          <input type="old_password" class="form-control rounded-3" name="old_password" id="old_password" value="{{ old('old_password') }}" placeholder="Старый пароль" required>
          <label for="old_password">Старый пароль</label>
        </div>
        <div class="form-floating mb-3">
          <input type="password" class="form-control rounded-3" name="password" id="password" placeholder="Новый пароль" required>
          <label for="password">Новый пароль</label>
        </div>
        <div class="form-floating mb-3">
          <input type="password" class="form-control rounded-3" name="password_confirmation" id="repeatPassword" placeholder="Повторно введите новый пароль" required>
          <label for="repeatPassword">Повторно введите новый пароль</label>
        </div>
        <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Сохранить</button>
        <a href="/{{ $lang }}/profile" class="w-100 mb-2 btn btn-lg rounded-3 btn-link">Назад</a>
      </form>
    </div>
  </div>

</x-app-layout>