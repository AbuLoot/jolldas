@extends('layout')

@section('meta_title', 'Tracks page')

@section('meta_description', 'Tracks page')

@section('head')

@endsection

@section('content')

  <div class="carousel slide mb-3" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="img/jolldas-2.jpg" class="d-block w-100 h-100" alt="...">
        <div class="carousel-caption d-none-d-md-block">
          <h1 class="d-none d-md-block fw-normal shadow-1">Отслеживание по трек коду</h1>
          <form action="/search-track" method="get" class="col-12 col-lg-8 offset-lg-2 mt-5 mt-lg-0 mb-3 mb-lg-0 me-lg-2 py-2" role="search">
            <input type="search" name="code" class="form-control form-control-dark form-control-lg text-bg-dark" placeholder="Введите трек код..." aria-label="Search" min="4" required>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="container my-3 my-lg-5">

      <div class="col-12 col-sm-12">
        <?php

          $statusClasses = [
            'arrived' => [
              'card-color' => 'bg-arrived',
              'item-color' => 'bg-secondary',
            ],
            'sent' => [
              'card-color' => 'bg-sent',
              'item-color' => 'bg-secondary',
            ],
            'waiting' => [
              'card-color' => 'bg-received',
              'item-color' => 'bg-warning',
            ],
            'received' => [
              'card-color' => 'bg-received',
              'item-color' => 'bg-warning',
            ],
            'added' => [
              'card-color' => 'bg-added',
              'item-color' => 'bg-muted',
            ],
          ];

        ?>
        @foreach($tracks as $track)
          <div class="track-item mb-2">

            <?php $activeStatus = $track->statuses->last(); ?>

            <div class="border {{ $statusClasses[$activeStatus->slug]['card-color'] }} rounded-top p-2" data-bs-toggle="collapse" href="#collapse{{ $track->id }}">
              <div class="row">
                <div class="col-12 col-lg-5">
                  <div><b>Track code:</b> {{ $track->code }}</div>
                  <div><b>Description:</b> {{ Str::limit($track->description, 35) }}</div>
                </div>
                <div class="col-12 col-lg-4">
                  <div><b>{{ ucfirst($activeStatus->slug) }} Date:</b> {{ $activeStatus->created_at }}</div>
                  <div><b>Status:</b> {{ $activeStatus->title }}</div>
                </div>
                @if($track->user) 
                  <div class="col-12 col-lg-3">
                    <b>User:</b> {{ $track->user->name.' '.$track->user->lastname }}<br>
                    <b>ID:</b> {{ $track->user->id_client }}
                  </div>
                @endif
              </div>
            </div>

            <div class="collapse" id="collapse{{ $track->id }}">
              <div class="border border-top-0 rounded-bottom p-3">
                <section>
                  <ul class="timeline-with-icons">
                    @foreach($track->statuses()->orderByDesc('id')->get() as $status)

                      @if($activeStatus->id == $status->id)
                        <li class="timeline-item mb-2">
                          <span class="timeline-icon bg-success"><i class="bi bi-check text-white"></i></span>
                          <p class="text-success mb-0">{{ $status->title }}</p>
                          <p class="text-success mb-0">{{ $status->created_at }}</p>
                        </li>
                        @continue
                      @endif

                      <li class="timeline-item mb-2">
                        <span class="timeline-icon bg-secondary"><i class="bi bi-check text-white"></i></span>
                        <p class="text-body mb-0">{{ $status->title }}</p>
                        <p class="text-body mb-0">{{ $status->created_at }}</p>
                      </li>
                    @endforeach
                  </ul>
                  <p><b>Description:</b> {{ $track->description }}</p>
                </section>
              </div>
            </div>
          </div>
        @endforeach

      </div>
  </div>

@endsection