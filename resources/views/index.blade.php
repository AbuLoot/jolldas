@extends('layout')

@section('meta_title', (!empty($page->meta_title)) ? $page->meta_title : $page->title)

@section('meta_description', (!empty($page->meta_description)) ? $page->meta_description : $page->title)

@section('head')

@endsection

@section('content')

  <div class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="img/jolldas-2.jpg" class="bg-jolldas d-block w-lg-100 h-100" alt="...">
        <div class="carousel-caption d-none-d-md-block pb-2">
          <div class="col-12 col-md-10 mx-auto text-bg-dark rounded-2 opacity-75 p-2 mb-3">
            <h1 class="display-4 fw-bold">Jolldas - верная логистика от Китая до адреса с&nbsp;гарантией</h1>
            <!-- <h5 class="text-info">"За 2023 год, мы доставили более 300,000 грузов".</h5> -->
          </div>

          <h3 class="d-noned-md-block fw-normal text-shadow-1">Отслеживание по трек коду</h3>
          <form action="/search-track" method="get" class="col-12 col-lg-8 offset-lg-2 mt-lg-0" role="search">
            <input type="search" name="code" class="form-control form-control-dark form-control-lg text-bg-dark" placeholder="Введите трек код..." aria-label="Search" min="4" required>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Guarantees & Calc -->
  <div class="container mb-5">
    <div class="row align-items-md-stretch">
      <div class="col-md-6 mb-3">
        <div class="h-100 py-5 px-4 bg-added border rounded-3">
          <h2>Гарантии</h2>

          <div class="container">
            <div class="row g-4 py-4">
              <div class="col-12 d-flex align-items-start">
                <span class="me-3"><i class="bi bi-shield-check fs-1"></i></span>
                <div>
                  <h3 class="mb-0 fs-4">Заключаем договор с&nbsp;клиентом на&nbsp;справедливых условиях</h3>
                </div>
              </div>
              <div class="col-12 d-flex align-items-start">
                <span class="me-3"><i class="bi bi-shield-check fs-1"></i></span>
                <div>
                  <h3 class="mb-0 fs-4">Возмещяем утерянным или&nbsp;испорченным по нашей вине грузам</h3>
                </div>
              </div>
              <div class="col-12 d-flex align-items-start">
                <span class="me-3"><i class="bi bi-shield-check fs-1"></i></span>
                <div>
                  <h3 class="mb-0 fs-4">Если груз задержался на месяц дадим скидку</h3>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="col-md-6">
        <div class="h-100 p-5 text-bg-secondary border rounded-3">
          <form method="POST" action="/calculate" id="calc">
            @csrf
            <h2 class="mb-4">{{ __('app.price_calculator') }}</h2>
            <div class="row">
              <div class="col-lg-3 col-6 mb-3">
                <label for="elLength" class="form-label">{{ __('app.length') }}</label>
                <input type="number" class="form-control" id="elLength" name="length" min="0" max="100" placeholder="0,0" value="{{ session('length') }}" step="any" required>
              </div>
              <div class="col-lg-3 col-6 mb-3">
                <label for="width" class="form-label">{{ __('app.width') }}</label>
                <input type="number" class="form-control" id="width" name="width" min="0" max="100" placeholder="0,0" value="{{ session('width') }}" step="any" required>
              </div>
              <div class="col-lg-3 col-6 mb-3">
                <label for="height" class="form-label">{{ __('app.height') }}</label>
                <input type="number" class="form-control" id="height" name="height" min="0" max="100" placeholder="0,0" value="{{ session('height') }}" step="any" required>
              </div>
              <div class="col-lg-3 col-6 mb-3">
                <label for="weight" class="form-label">{{ __('app.weight') }}</label>
                <input type="number" class="form-control" id="weight" name="weight" min="0" placeholder="0,0" value="{{ session('weight') }}" step="any" required>
              </div>
              <div class="col-lg-12 mb-3">
                <label class="form-label">{{ __('app.delivery_method') }}</label>
                <div class="list-group">
                  <label class="list-group-item d-flex gap-2">
                    <input class="form-check-input flex-shrink-0" type="radio" name="type_delivery" id="standart" value="1" checked>
                    <span>{{ __('app.standard_days') }}</span>
                  </label>
                  <label class="list-group-item d-flex gap-2">
                    <input class="form-check-input flex-shrink-0" type="radio" name="type_delivery" id="express" value="2">
                    <span>{{ __('app.express_days') }}</span>
                  </label>
                  <label class="list-group-item d-flex gap-2">
                    <input class="form-check-input flex-shrink-0" type="radio" name="type_delivery" id="express-clothes" value="3">
                    <span>{{ __('app.express_days_clothes') }}</span>
                  </label>
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('app.count') }}</button>

            @if(session('price'))
              <?php

                $typesDelivery = [
                  '1' => __('app.standard_days'),
                  '2' => __('app.express_days'),
                  '3' => __('app.express_days_clothes'),
                ];
              ?>
              <div id="text-hint">
                <hr>
                <div class="h3">{{ __('app.bulk_density') }}: <span id="density">{{ session('density') }}</span></div>
                <div class="h5">{{ __('app.delivery') }}: <span id="density">{{ $typesDelivery[session('typeDelivery')] }}</span></div>
                <div class="h3">{{ __('app.price') }}: $<span class="price">{{ session('price') }}</span></div>
                <div class="display-5">
                  {{ __('app.total') }}:
                  @if(in_array(session('densityRange')[0], ['100', '800', '1000']))
                    <span style="color: #20c997;" class="fw-bold">${{ session('price') }}</span>
                  @else
                    <span style="color: #20c997;" class="fw-bold">${{ session('weight') * session('price') }}</span>
                  @endif
                </div>
              </div>
            @endif
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Possibilities -->
  <div class="text-bg-dark mb-5">
    <div class="container py-5">
      <h2 class="mb-4 d-none d-md-block">Преимущества</h2>
      <div class="row row-cols-1 row-cols-md-2 align-items-md-center g-4">
        <div class="d-flex flex-column align-items-start gap-2">
          <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('/img/sections/andras-vas-Bd7gNnWJBkU-unsplash.jpg');">
            <div class="d-flex flex-column h-100 p-4 pb-3 text-white text-shadow-1">
              <h3 class="pt-5 mt-5 mb-4 lh-1 fw-bold">Возможности</h3>
              <p class="fs-5 text-shadow-1">Мы можем научить вас покупать товары дешевле из надежных магазинов. А стабильную доставку будем опеспечивать сами. Так, мы делаем возможным для вас заниматься бизнесом или просто делать выгодный шоппинг.</p>
              <p class="fs-5 text-shadow-1">За 2023 год, мы доставили более 300,000 грузов.</p>

            </div>
          </div>
          <!-- <h3 class="fw-bold">Возможности</h3> -->
          <!-- <p class="fs-5">Мы можем научить вас покупать товары дешевле из надежных магазинов. А стабильную доставку будем опеспечивать сами. Так, мы делаем возможным для вас заниматься бизнесом или просто делать выгодный шоппинг.</p> -->
        </div>
        <div class="row row-cols-1 row-cols-sm-2 g-4">
          <div class="d-flex flex-column gap-2">
            <div><i class="bi bi-hand-index text-info fs-1"></i></div>
            <h4 class="mb-0">Удобный веб-сервис для отслеживания посылок</h4>
          </div>

          <div class="d-flex flex-column gap-2">
            <div><i class="bi bi-cash-stack text-info fs-1"></i></div>
            <h4 class="mb-0">Даем низкие цены на рынке</h4>
          </div>

          <div class="d-flex flex-column gap-2">
            <div><i class="bi bi-person-video text-info fs-1"></i></div>
            <h4 class="mb-0">Консультируем, информируем и&nbsp;обучаем</h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style type="text/css">
    .stage-nums {
      width: 6rem;
      height: 4rem;
    }
  </style>

  <!-- How we works? -->
  <div class="container mb-5">
    <h2 class="text-center mb-5">Как мы работаем?</h2>

    <div class="row">
      <section class="col-12 col-md-10 offset-md-2 mb-5">
        <ul class="timeline-with-icons">
          <li class="timeline-item mb-2">
            <span class="timeline-icon bg-success text-white fs-2 p-4">1</span>
            <p class="ms-3 mb-4 pt-2 fs-5">Регистрируетесь на сайте и получаете личный ID код</p>
          </li>
          <li class="timeline-item mb-2">
            <span class="timeline-icon bg-success text-white fs-2 p-4">2</span>
            <p class="ms-3 mb-4 pt-2 fs-5">На интернет площадках указываете ID код и наш адрес склада в Китае</p>
          </li>
          <li class="timeline-item mb-2">
            <span class="timeline-icon bg-success text-white fs-2 p-4">3</span>
            <p class="ms-3 mb-4 pt-2 fs-5">Мы принимаем ваш заказ и вносим трек-код в веб-приложения Jolldas</p>
          </li>
          <li class="timeline-item mb-2">
            <span class="timeline-icon bg-success text-white fs-2 p-4">4</span>
            <p class="ms-3 mb-4 pt-2 fs-5">Хорошо упаковав товар, отправляем в Казахстан</p>
          </li>
          <li class="timeline-item mb-2">
            <span class="timeline-icon bg-success text-white fs-2 p-4">5</span>
            <p class="ms-3 mb-4 pt-2 fs-5">После прибытия оповещяем вас на сайте или же через почту</p>
          </li>
        </ul>
      </section>
    </div>

    <div class="row">
      <div class="ms-auto text-center">
        <a href="/login" class="btn btn-outline-dark btn-lg me-2">Вход</a>
        <a href="/register" class="btn btn-warning btn-lg">Регистрация</a>
      </div>
    </div>
  </div>

  <hr>

  <!-- Contacts -->
  <div class="container col-xl-10 col-xxl-8">
    <div class="row align-items-center g-lg-5 py-5">
      <div class="col-lg-7 text-center text-lg-start">
        <p class="display-5 fw-bold">Остались вопросы?<br> Или хотите обсудить партнерство?</p>
        <p class="display-5 fw-bold">Обращяйтесь.</p>
      </div>
      <div class="col-md-10 mx-auto col-lg-5">
        <form method="POST" action="/send-app" id="app-form" class="p-4 p-md-5 border rounded-3 bg-body-tertiary">
          @csrf
          @include('components.alerts')
          <h3 class="mb-3">{{ __('app.app_form') }}</h3>
          <div class="form-floating mb-3">
            <input type="text" name="name" class="form-control" id="form-name" minlength="2" maxlength="40" autocomplete="off" placeholder="{{ __('app.name') }}" required>
            <label for="form-name">{{ __('app.name') }}</label>
          </div>
          <div class="form-floating mb-3">
            <input type="email" name="email" class="form-control" id="form-email" autocomplete="off" placeholder="{{ __('app.email') }}" required>
            <label for="form-email">{{ __('app.email') }}</label>
          </div>
          <div class="form-floating mb-3">
            <input type="tel" id="form-number" class="form-control" pattern="(\+?\d[- .]*){7,13}" name="phone" minlength="5" maxlength="20" placeholder="{{ __('app.phone') }}" required>
            <label for="form-number">{{ __('app.phone') }}</label>
          </div>
          <div class="form-floating mb-3">
            <textarea class="form-control" name="message" placeholder="Leave a comment here" id="message"></textarea>
            <label for="message">{{ __('app.text') }}</label>
          </div>
          <button class="w-100 btn btn-lg btn-primary" type="submit">{{ __('app.send') }}</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Posts -->
  @if($posts->isNotEmpty())
    <div class="container my-3 my-lg-5">
      <div class="row gx-2 gy-2">
        @foreach($posts as $post)
          <div class="col">
            <div class="card shadow-sm">
              @if($post->image)
                <img src="/img/posts/{{ $post->image }}" class="card-img-top" alt="{{ $post->title }}">
              @endif

              <div class="card-body">
                <h5 class="card-title">{{ $post->title }}</h5>
                <p class="card-text">{!! Str::limit($post->content, 50) !!}</p>
                <a href="/i/news/{{ $post->slug }}" class="btn btn-link">Дальше</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- START THE FEATURETTES -->
  <div class="container">
    <br>
    @if(!empty($promo))
      {!! $promo->content !!}
    @endif

  </div>

@endsection

@section('scripts')

  @if (session('price'))
    <script>
      document.getElementById("calc").scrollIntoView({behavior: 'instant'});
    </script>
  @endif
  @if (count($errors) > 0 || session('status'))
    <script>
      document.getElementById("app-form").scrollIntoView({behavior: 'instant'});
    </script>
  @endif

@endsection