<div>
  <div class="px-3 py-3 border-bottom mb-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">

      <h4 class="col-4 col-lg-4 mb-md-2 mb-lg-0">Track codes</h4>

      <form class="col-8 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto">
        <input wire:model="search" type="search" class="form-control form-control-lg" placeholder="Enter track code..." aria-label="Search">
      </form>

    </div>
  </div>

  <div class="container">
    <ul class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a class="nav-link bg-light active" aria-current="page">Received</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{ $lang }}/storage/sending">Send</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{ $lang }}/storage/arrival">Arrival</a>
      </li>
    </ul>

    <div class="row">
      <div class="col-12 col-sm-3 mb-2">
        <form wire:submit.prevent="toReceive">
          <div class="form-floating mb-3">
            <input wire:model.defer="trackCode" type="text" class="form-control form-control-lg @error('trackCode') is-invalid @enderror" placeholder="Add track-code" id="trackCodeArea">
            <label for="trackCodeArea">Enter track code</label>
            @error('trackCode')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <button type="submit" id="toReceive" class="btn btn-primary btn-lg"><i class="bi bi-check2"></i> To receive</button>
        </form>
      </div>

      <div class="col-12 col-sm-9">
        @foreach($tracks as $track)
          <div class="track-item mb-2">

            <?php $activeStatus = $track->statuses->last(); ?>

            <div class="border {{ __('statuses.classes.'.$activeStatus->slug.'.card-color') }} rounded-top p-2" data-bs-toggle="collapse" href="#collapse{{ $track->id }}">
              <div class="row">
                <div class="col-12 col-lg-6">
                  <div><b>Track code:</b> {{ $track->code }}</div>
                  <div><b>Description:</b> {{ Str::limit($track->description, 35) }}</div>
                </div>
                <div class="col-12 col-lg-6">
                  <div><b>{{ ucfirst($activeStatus->slug) }} Date:</b> {{ $activeStatus->pivot->created_at }}</div>
                  <div><b>Status:</b> {{ $activeStatus->title }}</div>
                </div>
                @if($track->user) 
                  <div class="col-12 col-lg-12">
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

</div>

@section('scripts')
  <script type="text/javascript">
    // Toast Script
    window.addEventListener('area-focus', event => {

      var areaEl = document.getElementById('trackCodeArea');
      areaEl.value = '';
      areaEl.focus();
    })
  </script>
@endsection