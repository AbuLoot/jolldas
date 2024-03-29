<x-app-layout>
  <div class="row">
    <div class="col-lg-5 col-md-7 col-sm-9 mx-auto">

      <div class="p-4">
          {{ __('Забыли пароль? Без проблем. Просто введите свой email, и мы вышлем вам ссылку для сброса пароля.') }}
      </div>

			<!-- Session Status -->
			<x-auth-session-status class="mb-4" :status="session('status')" />

      <!-- Validation Errors -->
      <x-auth-validation-errors class="mb-4" :errors="$errors" />

			<form method="POST" action="{{ route('password.email') }}" class="p-4 p-md-5 bg-light border rounded-3 bg-light">
				@csrf

        <div class="form-floating mb-3">
          <input type="email" class="form-control rounded-3" id="email" name="email" :value="old('email')" placeholder="name@example.com" required autofocus>
          <label for="email">Email адрес</label>
        </div>
        <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">{{ __('Получить ссылку на почту') }}</button>

        @if (Route::has('password.request'))
          <a href="/verify-user" class="w-100 mb-2 btn btn-lg btn-link">{{ __('Другой способ') }}</a>
        @endif
      </form>

    </div>
  </div>
</x-app-layout>
