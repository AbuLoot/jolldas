<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Joystick Admin</title>
    <meta name="description" content="Joystick Admin">
    <meta name="author" content="issayev.adilet@gmail.com">
    <link rel="icon" href="/joystick/favicon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="/joystick/favicon.png" type="image/x-icon" />

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="/joystick/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/joystick/css/admin.css" rel="stylesheet">
    @yield('head')

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" id="sidebarCollapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand text-uppercase" href="/{{ $lang }}/admin"><i class="material-icons text-primary">sports_esports</i> <b>Joystick</b></a>
        </div>

        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                <i class="material-icons md-20">person_outline</i> {{ Auth::user()->name }} <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" role="menu">
                <li>
                  <a class="dropdown-item" href="{{ route('logout', $lang) }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Выйти') }} </a>
                  <form id="logout-form" action="{{ route('logout', $lang) }}" method="POST" style="display: none;">
                    @csrf
                  </form>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-7 col-sm-3 col-md-2 sidebar">

          <div class="btn-sidebar" role="button" data-toggle="collapse" href="#sidebarResources" aria-expanded="true" aria-controls="sidebarResources">Cargo <span class="caret"></span></div>
          <ul class="nav nav-sidebar collapse in" id="sidebarResources">
            <li @if(Request::is($lang.'/admin/tracks*')) class="active" @endif><a href="/{{ $lang }}/admin/tracks"><i class="material-icons md-20">local_shipping</i> Треки</a></li>
            <li @if(Request::is($lang.'/admin/statuses*')) class="active" @endif><a href="/{{ $lang }}/admin/statuses"><i class="material-icons md-20">done_all</i> Статусы</a></li>
          </ul>

          <div class="btn-sidebar" role="button" data-toggle="collapse" href="#sidebarContent" aria-expanded="true" aria-controls="sidebarContent">Контент <span class="caret"></span></div>
          <ul class="nav nav-sidebar collapse in" id="sidebarContent">
            @can('viewAny', App\Models\Page::class)<li @if(Request::is($lang.'/admin/pages*')) class="active" @endif><a href="/{{ $lang }}/admin/pages"><i class="material-icons md-20">content_copy</i> Страницы</a></li>@endcan
            @can('viewAny', App\Models\Post::class)<li @if(Request::is($lang.'/admin/posts*')) class="active" @endif><a href="/{{ $lang }}/admin/posts"><i class="material-icons md-20">create</i> Новости</a></li>@endcan
            @can('viewAny', App\Models\Section::class)<li @if(Request::is($lang.'/admin/sections*')) class="active" @endif> <a href="/{{ $lang }}/admin/sections"><i class="material-icons md-20">dashboard</i> Разделы</a> </li>@endcan
            @can('allow-filemanager', Auth::user())<li @if(Request::is($lang.'/admin/frame-filemanager*')) class="active" @endif><a href="/{{ $lang }}/admin/frame-filemanager"><i class="material-icons md-20">folder</i> Файловый менеджер</a></li>@endcan
            @can('viewAny', App\Models\Mode::class)<li @if(Request::is($lang.'/admin/modes*')) class="active" @endif><a href="/{{ $lang }}/admin/modes"><i class="material-icons md-20">style</i> Режимы</a></li>@endcan
            @can('viewAny', App\Models\App::class)<li @if(Request::is($lang.'/admin/apps*')) class="active" @endif><a href="/{{ $lang }}/admin/apps"><i class="material-icons md-20">send</i> Заявки</a></li>@endcan
          </ul>

          <div class="btn-sidebar" role="button" data-toggle="collapse" href="#sidebarResources" aria-expanded="true" aria-controls="sidebarResources">Ресурсы <span class="caret"></span></div>
          <ul class="nav nav-sidebar collapse in" id="sidebarResources">
            @can('viewAny', App\Models\Region::class)<li @if(Request::is($lang.'/admin/regions*')) class="active" @endif><a href="/{{ $lang }}/admin/regions"><i class="material-icons md-20">map</i> Регионы</a></li>@endcan
            @can('viewAny', App\Models\Language::class)<li @if(Request::is($lang.'/admin/languages*')) class="active" @endif><a href="/{{ $lang }}/admin/languages"><i class="material-icons md-20">language</i> Языки</a></li>@endcan
            @can('viewAny', App\Models\Company::class)<li @if(Request::is($lang.'/admin/companies*')) class="active" @endif><a href="/{{ $lang }}/admin/companies"><i class="material-icons md-20">business</i> Компании</a></li>@endcan
            @can('viewAny', App\Models\User::class)<li @if(Request::is($lang.'/admin/users*')) class="active" @endif><a href="/{{ $lang }}/admin/users"><i class="material-icons md-20">people_outline</i> Пользователи</a></li>@endcan
            @can('viewAny', App\Models\Role::class)<li @if(Request::is($lang.'/admin/roles*')) class="active" @endif><a href="/{{ $lang }}/admin/roles"><i class="material-icons md-20">accessibility</i> Роли</a></li>@endcan
            @can('viewAny', App\Models\Permission::class)<li @if(Request::is($lang.'/admin/permissions*')) class="active" @endif><a href="/{{ $lang }}/admin/permissions"><i class="material-icons md-20">lock_open</i> Права доступа</a></li>@endcan
          </ul>

          <ul class="nav nav-sidebar">
            <li><a href="{{ route('logout', $lang) }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="material-icons md-20">exit_to_app</i> Выйти</a></li>
          </ul>
        </div>

        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          @yield('content')
        </div>
      </div>
    </div>

    <script src="/joystick/js/jquery.min.js"></script>
    <script src="/joystick/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
          $('.sidebar').toggleClass('active');
        });

        $('.main').on('click', function () {
          $('.sidebar').removeClass('active');
        });
      });
    </script>
    @yield('scripts')
  </body>
</html>
