@guest
  <a href="/login" class="btn btn-outline-light btn-lg me-2">Вход</a>
  <a href="/register" class="btn btn-warning btn-lg">Регистрация</a>
@else
  <div class="flex-shrink-0 dropdown ms-md-auto ps-3">
    <a href="#" class="d-block link-light text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="bi bi-person-circle fs-4 text-white"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end text-small shadow">
      <div class="text-muted px-3 py-1">{{ Auth::user()->name . ' ' . Auth::user()->lastname }}</div>
      <li><a class="dropdown-item py-2" href="/{{ $lang }}/profile">Мой аккаунт</a></li>
      <li><a class="dropdown-item py-2" href="/{{ $lang }}/client">Мои треки</a></li>
      <li><a class="dropdown-item py-2" href="/{{ $lang }}/client/archive">Мой архив</a></li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <form method="POST" action="/logout">
          @csrf
          <a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
        </form>
      </li>
    </ul>
  </div>
@endguest