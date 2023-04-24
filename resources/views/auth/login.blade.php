<x-app-layout>
  <div class="row">
    <div class="col-lg-5 col-md-7 col-sm-9 mx-auto">

      <!-- Session Status -->
      <x-auth-session-status class="mb-4" :status="session('status')" />

      <!-- Validation Errors -->
      <x-auth-validation-errors class="mb-4" :errors="$errors" />

      <form method="POST" action="{{ route('login') }}" class="p-4 p-md-5 bg-light border rounded-3 bg-light">
        @csrf
        <h2 class="fw-bold mb-0">Войти</h2>
        <br>

        <div class="form-floating mb-3">
          <input type="email" class="form-control rounded-3" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required>
          <label for="email">Email адрес</label>
        </div>
        <!-- <div class="form-floating mb-3">
          <input type="tel" class="form-control rounded-3" id="tel" name="tel" placeholder="Номер телефона">
          <label for="tel">Номер телефона</label>
        </div> -->
        <div class="form-floating mb-3">
          <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Введите пароль" required>
          <label for="password">Введите пароль</label>
        </div>

        <div class="checkbox mb-3">
          <label>
            <input type="checkbox" name="remember" value="remember-me"> Запомнить меня
          </label>
        </div>
        <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Войти</button>
        @if (Route::has('password.request'))
          <a href="/verify-user" class="w-100 mb-2 btn btn-lg btn-link">{{ __('Забыли пароль?') }}</a>
        @endif
      </form>

    </div>
  </div>
</x-app-layout>
