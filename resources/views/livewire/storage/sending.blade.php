<div>

  <div class="px-3 py-3 border-bottom mb-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">

      <h4 class="col-12 col-lg-4 mb-md-2 mb-lg-0">Track codes</h4>

      <form class="col-12 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto">
        <input wire:model="search" type="search" class="form-control form-control-lg" placeholder="Enter track code..." aria-label="Search">
      </form>

    </div>
  </div>

  <div class="container">

    @foreach($tracks as $track)
      <div class="track-item mb-2">

        <?php $activeStatus = $track->statuses->last(); ?>
        <div class="row">
          <div class="col-10 col-lg-10">
            <div class="border {{ __('statuses.classes.'.$activeStatus->slug.'.card-color') }} rounded-top p-2" data-bs-toggle="collapse" href="#collapse{{ $track->id }}">
              <div class="row">
                <div class="col-12 col-lg-5">
                  <div><b>Track code:</b> {{ $track->code }}</div>
                  <div><b>Description:</b> {{ Str::limit($track->description, 35) }}</div>
                </div>
                <div class="col-12 col-lg-4">
                  <div><b>{{ ucfirst($activeStatus->slug) }} Date:</b> {{ $activeStatus->pivot->created_at }}</div>
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
          <div class="col-2 col-lg-2 text-end">
            <div class="d-grid">
              <button  wire:click="btnToSend('{{ $track->code }}')" type="button" wire:loading.attr="disabled" class="btn btn-primary"><i class="bi bi-send"></i> <span class="d-none d-sm-inline">To send</span></button>
            </div>
          </div>
        </div>
      </div>
    @endforeach

    <ul class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a class="nav-link" href="/{{ $lang }}/storage">Reception</a>
      </li>
      <li class="nav-item dropdown">
        <?php $icons = ['list' => 'card-checklist', 'group' => 'collection']; ?>
        <a class="nav-link bg-light active dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
          <i class="bi bi-{{ $icons[$mode] }}"></i> Send
        </a>
        <ul class="dropdown-menu">
          <li><a wire:click="setMode('list')" class="dropdown-item" href="#"><i class="bi bi-card-checklist"></i> List tracks</a></li>
          <li><a wire:click="setMode('group')" class="dropdown-item" href="#"><i class="bi bi-collection"></i> Group tracks</a></li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{ $lang }}/storage/sorting"><i class="bi bi-dpad"></i> Sorting</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{ $lang }}/storage/arrival">Arrival</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{ $lang }}/storage/giving">Giving</a>
      </li>
    </ul>

    <div class="row">
      <div class="col-12 col-sm-3 mb-2">
        <form wire:submit.prevent="toSend">
          <div class="form-floating mb-3">
            <input wire:model.defer="trackCode" type="text" class="form-control form-control-lg @error('trackCode') is-invalid @enderror" placeholder="Add track-code" id="trackCodeArea">
            <label for="trackCodeArea">Enter track code</label>
            @error('trackCode')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <button type="submit" id="toSend" wire:loading.attr="disabled" class="btn btn-primary btn-lg"><i class="bi bi-send"></i> To send</button>
        </form>
      </div>
      <div class="col-12 col-sm-9">
        @if($mode == 'group')

          <?php
            // Dates
            $now          = now();
            $today        = $now->copy()->format('Y-m-d');
            $yesterday    = $now->copy()->subDay(1)->format('Y-m-d');
            $twoDaysAgo   = $now->copy()->subDay(2)->format('Y-m-d');
            $threeDaysAgo = $now->copy()->subDay(3)->format('Y-m-d');
            $fourDaysAgo  = $now->copy()->subDay(4)->format('Y-m-d');
            $fiveDaysAgo  = $now->copy()->subDay(5)->format('Y-m-d');
            $sixDaysAgo   = $now->copy()->subDay(6)->format('Y-m-d');
            $previousWeek = $now->copy()->startOfWeek()->subWeek(2)->format('Y-m-d');
            $twoWeekAgo   = $now->copy()->startOfWeek()->subWeek(3)->format('Y-m-d');

            // Grouped by date
            $todayGroup         = $receivedTracks->where('updated_at', '>', $yesterday.' 23:59:59')->where('updated_at', '<=', now());
            $yesterdayGroup     = $receivedTracks->where('updated_at', '>=', $yesterday)->where('updated_at', '<', $today);
            $twoDaysAgoGroup    = $receivedTracks->where('updated_at', '>', $twoDaysAgo)->where('updated_at', '<', $yesterday);
            $threeDaysAgoGroup  = $receivedTracks->where('updated_at', '>', $threeDaysAgo)->where('updated_at', '<', $twoDaysAgo);
            $fourDaysAgoGroup   = $receivedTracks->where('updated_at', '>', $fourDaysAgo)->where('updated_at', '<', $threeDaysAgo);
            $fiveDaysAgoGroup   = $receivedTracks->where('updated_at', '>', $fiveDaysAgo)->where('updated_at', '<', $fourDaysAgo);
            $sixDaysAgoGroup    = $receivedTracks->where('updated_at', '>', $sixDaysAgo)->where('updated_at', '<', $fiveDaysAgo);
            $previousWeekGroup  = $receivedTracks->where('updated_at', '>', $previousWeek)->where('updated_at', '<', $sixDaysAgo);
            $twoWeekAgoGroup    = $receivedTracks->where('updated_at', '>', $twoWeekAgo)->where('updated_at', '<', $previousWeek);
            $prevTimeGroup      = $receivedTracks->where('updated_at', '<', $twoWeekAgo);

            $allTracksGroups = [
              'today' => [
                'dateFrom' => $yesterday,
                'dateTo'   => now()->format('Y-m-d H-i'),
                'dateName' => 'Today',
                'group' => $todayGroup,
              ],
              'yesterday' => [
                'dateFrom' => $yesterday,
                'dateTo'   => $today,
                'dateName' => 'Yesterday',
                'group' => $yesterdayGroup,
              ],
              'twoDaysAgo' => [
                'dateFrom' => $twoDaysAgo,
                'dateTo'   => $yesterday,
                'dateName' => 'Two Days Ago',
                'group' => $twoDaysAgoGroup,
              ],
              'threeDaysAgo' => [
                'dateFrom' => $threeDaysAgo,
                'dateTo'   => $twoDaysAgo,
                'dateName' => 'Three Days Ago',
                'group' => $threeDaysAgoGroup,
              ],
              'fourDaysAgo' => [
                'dateFrom' => $fourDaysAgo,
                'dateTo'   => $threeDaysAgo,
                'dateName' => 'Four Days Ago',
                'group' => $fourDaysAgoGroup,
              ],
              'fiveDaysAgo' => [
                'dateFrom' => $fiveDaysAgo,
                'dateTo'   => $fourDaysAgo,
                'dateName' => 'Five Days Ago',
                'group' => $fiveDaysAgoGroup,
              ],
              'sixDaysAgo' => [
                'dateFrom' => $sixDaysAgo,
                'dateTo'   => $fiveDaysAgo,
                'dateName' => 'Six Days Ago',
                'group' => $sixDaysAgoGroup,
              ],
              'previousWeek' => [
                'dateFrom' => $previousWeek,
                'dateTo'   => $sixDaysAgo,
                'dateName' => 'Previous Week',
                'group' => $previousWeekGroup,
              ],
              'twoWeekAgo' => [
                'dateFrom' => $twoWeekAgo,
                'dateTo'   => $previousWeek,
                'dateName' => 'Two Week Ago',
                'group' => $twoWeekAgoGroup,
              ],
              'prev' => [
                // 'dateFrom' => now()->endOfWeek()->subWeek(4)->format('Y-m-d'),
                'dateFrom' => $twoWeekAgo,
                'dateTo'   => null,
                'dateName' => 'For a Long Time',
                'group' => $prevTimeGroup,
              ],
            ];

          ?>

          @foreach($allTracksGroups as $group)
            @if($group['group']->count())
              <div class="tracks-group mb-2">
                <div class="border bg-received rounded p-2">
                  <div class="row">
                    <div class="col-6 col-md-3">
                      <div><b>Date:</b> {{ $group['dateFrom'] }}</div>
                      <div><b>Count:</b> {{ $group['group']->count() }}pcs</div>
                    </div>
                    <div class="col-6 col-md-4"><b>Received: {{ $group['dateName'] }}</b></div>
                    <div class="col-12s col-md-5 text-end">
                      <button type="button" wire:click="openGroupByDate('{{ $group['dateFrom'] }}', '{{ $group['dateTo'] }}')" wire:loading.attr="disabled" class="btn btn-primary btn-lg">Open</button>
                      <button type="button" wire:click="sendGroupByDate('{{ $group['dateFrom'] }}', '{{ $group['dateTo'] }}')" wire:loading.attr="disabled" onclick="return confirm('Сonfirm action?') || event.stopImmediatePropagation()" class="btn btn-success btn-lg"><i class="bi bi-send"></i> Send group</button>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @endforeach

        @else
          @foreach($receivedTracks as $track)
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
          <br>
          <nav aria-label="Page navigation">
            {{ $receivedTracks->links() }}
          </nav>
        @endif
      </div>
    </div>
    <br>
  </div>

  <!-- Track Codes Modal -->
  <div class="modal fade" id="trackCodesModal" tabindex="-1" aria-labelledby="trackCodesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="trackCodesModalLabel">Track codes</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row gx-2">
            @foreach($trackCodes as $trackCode)
              <div class="col-7"><b>TC:</b> {{ $trackCode->code }}</div>
              <div class="col-5">{{ $trackCode->description }}</div>
            @endforeach
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    window.addEventListener('open-modal', event => {
      var tracksModal = new bootstrap.Modal(document.getElementById("trackCodesModal"), {});
      tracksModal.show();
    })
  </script>
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