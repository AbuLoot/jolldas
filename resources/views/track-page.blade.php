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

        @foreach($tracks as $track)
          <div class="track-item mb-2">

            <?php
              $activeStatus = $track->statuses->last();

              $sortedOrArrivalOrGivenRegion = null;

              if (in_array($activeStatus->slug, ['sorted', 'arrived', 'given']) OR in_array($activeStatus->id, [4, 5, 6])) {

                $sortedOrArrivalOrGivenRegion = $track->regions->last()->title ?? __('statuses.regions.title');
                $sortedOrArrivalOrGivenRegion = '('.$sortedOrArrivalOrGivenRegion.', Казахстан)';
              }
            ?>
            <div class="border {{ __('statuses.classes.'.$activeStatus->slug.'.card-color') }} rounded-top p-2" data-bs-toggle="collapse" href="#collapse{{ $track->id }}">
              <div class="row">
                <div class="col-12 col-lg-5">
                  <div><b>Track code:</b> {{ $track->code }}</div>
                  <div><b>Description:</b> {{ Str::limit($track->description, 5) }}</div>
                </div>
                <div class="col-12 col-lg-4">
                  <div><b>{{ ucfirst($activeStatus->slug) }} Date:</b> {{ $track->updated_at }}</div>
                  <div><b>Status:</b> {{ $activeStatus->title }} {{ $sortedOrArrivalOrGivenRegion }}</div>
                </div>
                @if($track->user) 
                  <div class="col-12 col-lg-3">
                    <b>User:</b> {{ $track->user->name.' '.\Str::limit($track->user->lastname, 1, '.') }}<br>
                    <b>ID:</b> {{ \Str::limit($track->user->id_client, 8) }}<br>
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
                          <p class="text-success mb-0">{{ $status->title }} {{ $sortedOrArrivalOrGivenRegion }}</p>
                          <p class="text-success mb-0">{{ $status->pivot->created_at }}</p>
                        </li>
                        @continue
                      @endif

                      <li class="timeline-item mb-2">
                        <span class="timeline-icon bg-secondary"><i class="bi bi-check text-white"></i></span>
                        <p class="text-body mb-0">{{ $status->title }}</p>
                        <p class="text-body mb-0">{{ $status->pivot->created_at }}</p>
                      </li>
                    @endforeach
                  </ul>
                  <p><b>Description:</b> {{ Str::limit($track->description, 5) }}</p>
                </section>
              </div>
            </div>
          </div>
        @endforeach

      </div>
  </div>

@endsection