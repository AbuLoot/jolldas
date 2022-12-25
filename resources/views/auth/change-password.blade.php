<x-app-layout>

  <div class="row">
    <div class="col-lg-5 col-md-7 col-sm-9 mx-auto">

      @include('components.alerts')

      <form action="/change-password" method="post" class="p-4 p-md-5 bg-light border rounded-3 bg-light">
        @csrf

        <h2 class="fw-bold mb-0">Изменение пароля</h2>
        <br>

        <div class="form-floating mb-3">
          <input type="password" class="form-control rounded-3" name="password" id="password" placeholder="Новый пароль" required>
          <label for="password">Новый пароль</label>
        </div>
        <div class="form-floating mb-3">
          <input type="password" class="form-control rounded-3" name="password_confirmation" id="repeatPassword" placeholder="Повторно введите новый пароль" required>
          <label for="repeatPassword">Повторно введите новый пароль</label>
        </div>
        <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Сохранить</button>
      </form>
    </div>
  </div>

</x-app-layout>