<div>
  <div class="py-3 border-bottom mb-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">

      <h4 class="col-12 col-lg-4 mb-md-2 mb-lg-0">Трек посылки</h4>

      <form class="col-10 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto">
        <div class="input-group">
          <input wire:model="search" type="search" class="form-control form-control-lg w-75" placeholder="Введите трек код..." aria-label="Search">

          <select wire:model="statusId" class="form-select w-25">
            <option value="0">Все</option>
            @foreach($statuses as $status)
              <option value="{{ $status->id }}">{{ $status->title }}</option>
            @endforeach
          </select>
        </div>
      </form>

      <div class="col-2 col-lg-4 text-end ms-md-auto ms-lg-0">
        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalAddTrack">
          <i class="bi bi-plus-circle-fill me-sm-2"></i> <span class="d-none d-md-inline">Добавить трек</span>
        </button>
      </div>

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
    <div class="alert alert-info">
      <h2>Jolldas переходит на сайт <a href="https://jibekjol.kz/kz">Jibekjol</a>. Используйте свой прежний логин и пароль, чтобы пользоваться сервисом.</h2>
      <div><a href="https://jibekjol.kz/kz" class="btn btn-primary btn-lg">Перейти на jibekjol.kz </a></div>
    </div>
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
            'sent-locally' => null,
            'given' => '<i class="bi bi-person-check-fill"></i>',
          ];

          $trackAndRegion = null;

          if (in_array($activeStatus->slug, ['sorted', 'arrived', 'sent-locally', 'given']) OR in_array($activeStatus->id, [4, 5, 6, 7])) {

            $trackAndRegion = $track->regions->last()->title ?? __('statuses.regions.title');
            $trackAndRegion = '('.$trackAndRegion.', Казахстан)';
          }
        ?>
        <div class="row gx-2">
          <div class="col-10 col-lg-11">
            <div class="border {{ __('statuses.classes.'.$activeStatus->slug.'.card-color') }} rounded-top p-2" data-bs-toggle="collapse" href="#collapse{{ $track->id }}">
              <div class="row">
                <div class="col-12 col-lg-5">
                  <div><b>Трек-код:</b> {{ $track->code }}</div>
                  <div><b>Описание:</b> {{ Str::limit($track->description, 35) }}</div>
                  @if($track->text)
                    <div><b>Text:</b> {{ $track->text }}</div>
                  @endif
                </div>
                <div class="col-9 col-lg-5">
                  <div><b>Дата:</b> {{ $track->updated_at }}</div>
                  <div><b>Статус: {!! $givenIcon[$activeStatus->slug] !!}</b> {{ $activeStatus->title }} {{ $trackAndRegion }}</div>
                </div>
              </div>
            </div>

            <div class="collapse" id="collapse{{ $track->id }}">
              <div class="border border-top-0 rounded-bottom p-3">
                <section>
                  <ul class="timeline-with-icons">
                    @foreach($track->statuses()->orderByPivot('created_at', 'desc')->get() as $status)

                      @if($activeStatus->id == $status->id)
                        <li class="timeline-item mb-2">
                          <span class="timeline-icon bg-success"><i class="bi bi-check text-white"></i></span>
                          <p class="text-success mb-0">{{ $status->title }} {{ $trackAndRegion }}</p>
                          <p class="text-success mb-0">{{ $status->pivot->created_at }}</p>
                        </li>
                        @continue
                      @endif

                      <li class="timeline-item mb-2">
                        <span class="timeline-icon bg-secondary"><i class="bi bi-check text-white"></i></span>
                        <p class="text-body mb-0">
                          {{ $status->title }}
                          @if($status->pivot->region_id)
                            ({{ $regions->firstWhere('id', $status->pivot->region_id)->title ?? __('statuses.regions.title') }}, Казахстан)
                          @endif
                        </p>
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
            <button wire:click="editTrack({{ $track->id }})" type="button" class="btn btn-outline-primary mb-1"><i class="bi bi-pen"></i></button>
            @if($track->status == 1)
              <button onclick="return confirm('Удалить запись?') || event.stopImmediatePropagation()" wire:click="deleteTrack({{ $track->id }})" type="button" class="btn btn-outline-dark"><i class="bi bi-x-lg"></i></button>
            @else
              <button wire:click="archiveTrack({{ $track->id }})" type="button" class="btn btn-outline-dark"><i class="bi bi-archive"></i></button>
            @endif
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

  <!-- Modal Agreement -->
  @if(Auth::user()->status == 'test')

    <div class="container">
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgreement">
        Подписать договор
      </button>
    </div>

    <livewire:client.sign-an-agreement>

    <br>
  @endif

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