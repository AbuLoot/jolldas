<div>
  <div class="px-3 py-3 border-bottom mb-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <!-- <div class="row"> -->
        <h4 class="col-12 col-lg-4 mb-md-2 mb-lg-0">Tracks</h4>

        <form class="col-10 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto">
          <input wire:model="search" type="search" class="form-control form-control-lg" placeholder="Enter track code..." aria-label="Search">
        </form>

        <div class="col-2 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto text-end">
          <button type="button" class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#filters" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Фильтр"><i class="bi bi-funnel-fill"></i> <span class="d-none d-sm-inline">Filters</span></button>
        </div>
      <!-- </div> -->
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-12 col-sm-12">

        <p><b>Count {{ $tracksCount }}pcs</b> @if($tracksStatus) | <b>{{ ucfirst($statuses->where('id', $tracksStatus)->first()->slug) }} tracks</b> @endif</p>

        @foreach($tracks as $track)
          <div class="track-item mb-2">
            <?php
              $activeStatus = $track->statuses->last();

              $givenIcon = [
                'added' => null,
                'received' => null,
                'sent' => null,
                'sorted' => null,
                'waiting' => null,
                'arrived' => null,
                'given' => '<i class="bi bi-person-check-fill"></i>',
              ];

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
                  <div><b>Description:</b> {{ Str::limit($track->description, 35) }}</div>
                </div>
                <div class="col-12 col-lg-4">
                  <div><b>{{ ucfirst($activeStatus->slug) }} Date:</b> {{ $activeStatus->pivot->created_at }}</div>
                  <div>
                    <b>Status: {!! $givenIcon[$activeStatus->slug] !!}</b> {{ $activeStatus->title }} {{ $sortedOrArrivalOrGivenRegion }}
                  </div>
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
                  <p><b>Description:</b> {{ $track->description }}</p>
                </section>
              </div>
            </div>
          </div>
        @endforeach

      </div>
    </div>

    <br>
    <nav aria-label="Page navigation">
      {{ $tracks->links() }}
    </nav>
  </div>

  <!-- Modal of Filter -->
  <div wire:ignore.self class="modal fade" id="filters" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="filterModalLabel">Filters</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form wire:submit.prevent="applyFilter">
            
            <div class="mb-3">
              <label for="statuses" class="form-label">Sorting by statuses</label><br>
              <select wire:model.defer="tracksStatus" class="form-select form-select-lg" id="statuses" aria-label="Default select example">
                  <option value="0">All</option>
                @foreach($statuses as $status)
                  <option value="{{ $status->id }}">{{ ucfirst($status->slug) }}</option>
                @endforeach
              </select>
            </div>

            <div class="mb-3">
              <label for="sort" class="form-label">Sorting by date</label><br>
              <select wire:model.defer="sort" class="form-select form-select-lg" id="sort" aria-label="Default select example">
                <option value="desc">Newest first</option>
                <option value="asc">Oldest first</option>
              </select>
            </div>

            <div class="row">
              <div class="col d-grid" role="group" aria-label="Basic example">
                <button wire:click="resetFilter" type="reset" class="btn btn-dark btn-lg">Reset</button>
              </div>
              <div class="col d-grid" role="group" aria-label="Basic example">
                <button type="submit" class="btn btn-primary btn-lg" data-bs-dismiss="modal">Apply</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
