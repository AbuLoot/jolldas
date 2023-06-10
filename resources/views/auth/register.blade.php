<x-app-layout>
  <div class="row">
    <div class="col-lg-5 col-md-7 col-sm-9 mx-auto">

      <!-- Validation Errors -->
      <x-auth-validation-errors class="mb-4" :errors="$errors" />

      <form method="POST" action="{{ route('register') }}" class="p-4 p-md-5 bg-light border rounded-3 bg-light">
        @csrf

        <h2 class="fw-bold mb-0">Регистрация</h2>
        <br>

        <div class="row">
          <div class="col">
            <div class="form-floating mb-3">
              <input type="text" name="name" class="form-control rounded-3" id="name" placeholder="Имя" value="{{ old('name') }}" required autofocus>
              <label for="name">Имя</label>
            </div>
          </div>
          <div class="col">
            <div class="form-floating mb-3">
              <input type="text" name="lastname" class="form-control rounded-3" id="lastname" placeholder="Отчество" value="{{ old('lastname') }}" required>
              <label for="lastname">Отчество</label>
            </div>
          </div>
        </div>
        <div class="form-floating mb-3">
          <input type="tel" class="form-control rounded-3" name="tel" id="tel" placeholder="Номер телефона" value="{{ old('tel') }}" required>
          <label for="tel">Номер телефона</label>
        </div>
        <div class="form-floating mb-3">
          <input type="email" class="form-control rounded-3" name="email" id="email" placeholder="name@example.com" value="{{ old('email') }}" required>
          <label for="email">Email адрес</label>
        </div>
        <div class="form-floating mb-3">
          <select class="form-control" name="region_id" id="region_id" required>
            <option value="">Выберите город...</option>
            <?php $traverse = function ($nodes, $prefix = null) use (&$traverse) { ?>
              <?php foreach ($nodes as $node) : ?>
                <option value="{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                <?php $traverse($node->children, $prefix.'___'); ?>
              <?php endforeach; ?>
            <?php }; ?>
            <?php $regions = \App\Models\Region::orderBy('sort_id')->get()->toTree(); ?>
            <?php $traverse($regions); ?>
          </select>
          <label for="region_id">Регион</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" class="form-control rounded-3" name="address" id="address" placeholder="Адрес" value="{{ old('address') }}" required>
          <label for="address">Адрес</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" class="form-control rounded-3" name="id_client" id="id_client" value="{{ old('id_client') }}" placeholder="ID account: J7799...">
          <label for="id_client">ID account</label>
        </div>
        <div class="form-floating mb-3">
          <input type="password" class="form-control rounded-3" name="password" id="password" placeholder="Введите пароль" required>
          <label for="password">Введите пароль</label>
        </div>
        <div class="form-floating mb-3">
          <input type="password" class="form-control rounded-3" name="password_confirmation" id="password_confirmation" placeholder="Повторно введите пароль" required>
          <label for="password_confirmation">Повторно введите пароль</label>
        </div>

        <button type="submit" class="w-100 mb-2 btn btn-lg rounded-3 btn-primary">Зарегистрироваться</button>
        <!-- <hr class="my-4"> -->
        <!-- <small class="text-muted">By clicking Sign up, you agree to the terms of use.</small> -->
      </form>
    </div>
  </div>
</x-app-layout>
