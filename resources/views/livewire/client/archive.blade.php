<div>
  <div class="px-3 py-3 border-bottom mb-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">

      <h4 class="col-12 col-lg-4 mb-md-2 mb-lg-0">Архив треков</h4>

      <form class="col-12 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto">
        <input wire:model="search" type="search" class="form-control form-control-lg" placeholder="Введите трек код..." aria-label="Search">
      </form>

    </div>
  </div>

  <!-- Toast notification -->
  <div class="toast-container position-fixed end-0 p-4">
    <div class="toast align-items-center text-bg-info border-0" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body text-white" id="toastBody"></div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <div class="container">

    <!-- Content -->
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
        <div class="row">
          <div class="col-10 col-lg-11">
            <div class="border {{ __('statuses.classes.'.$activeStatus->slug.'.card-color') }} rounded-top p-2" data-bs-toggle="collapse" href="#collapse{{ $track->id }}">
              <div class="row">
                <div class="col-12 col-lg-5">
                  <div><b>Трек-код:</b> {{ $track->code }}</div>
                  <div><b>Описание:</b> {{ Str::limit($track->description, 35) }}</div>
                </div>
                <div class="col-9 col-lg-5">
                  <div><b>Дата:</b> {{ $track->updated_at }}</div>
                  <div><b>Статус: {!! $givenIcon[$activeStatus->slug] !!}</b> {{ $activeStatus->title }} {{ $sortedOrArrivalOrGivenRegion }}</div>
                </div>
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
                  <p><b>Описание:</b> {{ $track->description }}</p>
                </section>
              </div>
            </div>
          </div>
          <div class="col-2 col-lg-1 text-end">
            <button wire:click="toggleTrack({{ $track->id }})" type="button" class="btn btn-outline-primary mb-1"><i class="bi bi-toggle2-on"></i></button>
          </div>
        </div>
      </div>
    @endforeach

    <br>
    <nav aria-label="Page navigation example">
      {{ $tracks->links() }}
    </nav>
  </div>

  <!-- Modal Add Track -->
  <livewire:client.add-track>

  <!-- Modal Edit Track -->
  <livewire:client.edit-track>

  <script>
    window.addEventListener('open-modal', event => {
      var trackModal = new bootstrap.Modal(document.getElementById("modalEditTrack"), {});
      trackModal.show();
    })
  </script>
</div>

@section('scripts')
  <script type="text/javascript">
    window.addEventListener('show-toast', event => {
      if (event.detail.selector) {
        const btnCloseModal = document.getElementById(event.detail.selector)
        btnCloseModal.click()
      }

      const toast = new bootstrap.Toast(document.getElementById('liveToast'))
      toast.show()

      const toastBody = document.getElementById('toastBody')
      toastBody.innerHTML = event.detail.message
    })
  </script>
@endsection