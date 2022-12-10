<div>

  <div class="px-3 py-3 border-bottom mb-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">

      <h4 class="col-12 col-lg-4 mb-md-2 mb-lg-0">Track codes group</h4>

      <form class="col-4 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto">
        <input wire:model="search" type="search" class="form-control form-control-lg" placeholder="Enter track code..." aria-label="Search">
      </form>

    </div>
  </div>

  <main class="container">
    <?php 
      // Dates
      $today        = now()->format('Y-m-d');
      $yesterday    = now()->subDay(1)->format('Y-m-d');
      $twoDaysAgo   = now()->subDay(2)->format('Y-m-d');
      $threeDaysAgo = now()->subDay(3)->format('Y-m-d');
      $fourDaysAgo  = now()->subDay(4)->format('Y-m-d');
      $fiveDaysAgo  = now()->subDay(5)->format('Y-m-d');
      $sixDaysAgo   = now()->subDay(6)->format('Y-m-d');
      $previousWeek = now()->startOfWeek()->subWeek(2)->format('Y-m-d');
      $twoWeekAgo   = now()->startOfWeek()->subWeek(3)->format('Y-m-d');

      // Grouped by date
      $todayGroup         = $tracksGroup->where('created_at', '>', $yesterday.' 23:59:59')->where('created_at', '<=', now());
      $yesterdayGroup     = $tracksGroup->where('created_at', '>', $yesterday)->where('created_at', '<', $today);
      $twoDaysAgoGroup    = $tracksGroup->where('created_at', '>', $twoDaysAgo)->where('created_at', '<', $yesterday);
      $threeDaysAgoGroup  = $tracksGroup->where('created_at', '>', $threeDaysAgo)->where('created_at', '<', $twoDaysAgo);
      $fourDaysAgoGroup   = $tracksGroup->where('created_at', '>', $fourDaysAgo)->where('created_at', '<', $threeDaysAgo);
      $fiveDaysAgoGroup   = $tracksGroup->where('created_at', '>', $fiveDaysAgo)->where('created_at', '<', $fourDaysAgo);
      $sixDaysAgoGroup    = $tracksGroup->where('created_at', '>', $sixDaysAgo)->where('created_at', '<', $fiveDaysAgo);
      $previousWeekGroup  = $tracksGroup->where('created_at', '>', $previousWeek)->where('created_at', '<', $sixDaysAgo);
      $twoWeekAgoGroup    = $tracksGroup->where('created_at', '>', $twoWeekAgo)->where('created_at', '<', $previousWeek);
      $prevTimeGroup      = $tracksGroup->where('created_at', '<', $twoWeekAgo);

      $allTracksGroups = [
        'today' => [
          'date' => $today,
          'dateName' => 'Today',
          'group' => $todayGroup,
        ],
        'yesterday' => [
          'date' => $yesterday,
          'dateName' => 'Yesterday',
          'group' => $yesterdayGroup,
        ],
        'twoDaysAgo' => [
          'date' => $twoDaysAgo,
          'dateName' => 'Two Days Ago',
          'group' => $twoDaysAgoGroup,
        ],
        'threeDaysAgo' => [
          'date' => $threeDaysAgo,
          'dateName' => 'Three Days Ago',
          'group' => $threeDaysAgoGroup,
        ],
        'fourDaysAgo' => [
          'date' => $fourDaysAgo,
          'dateName' => 'Four Days Ago',
          'group' => $fourDaysAgoGroup,
        ],
        'fiveDaysAgo' => [
          'date' => $fiveDaysAgo,
          'dateName' => 'Five Days Ago',
          'group' => $fiveDaysAgoGroup,
        ],
        'sixDaysAgo' => [
          'date' => $sixDaysAgo,
          'dateName' => 'Six Days Ago',
          'group' => $sixDaysAgoGroup,
        ],
        'previousWeek' => [
          'date' => $previousWeek,
          'dateName' => 'Previous Week',
          'group' => $previousWeekGroup,
        ],
        'twoWeekAgo' => [
          'date' => $twoWeekAgo,
          'dateName' => 'Two Week Ago',
          'group' => $twoWeekAgoGroup,
        ],
        'prev' => [
          'date' => now()->endOfWeek()->subWeek(4)->format('Y-m-d'),
          'dateName' => 'For a Long Time',
          'group' => $prevTimeGroup,
        ],
      ];
    ?>
    <ul class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a class="nav-link" href="/{{ $lang }}/storage">Received</a>
      </li>
      <li class="nav-item">
        <a class="nav-link bg-light active" aria-current="page">To send</a>
      </li>
    </ul>

    <div class="row">
      <div class="col-12 col-sm-12">

        @foreach($allTracksGroups as $group)
          @if($group['group']->count())
            <div class="tracks-group mb-2">
              <div class="border bg-received rounded p-2">
                <div class="row">
                  <div class="col-6 col-md-4">
                    <div><b>Date:</b> {{ $group['date'] }}</div>
                    <div><b>Count:</b> {{ $group['group']->count() }}pcs</div>
                  </div>
                  <div class="col-6 col-md-4"><b>Received: {{ $group['dateName'] }}</b></div>
                  <div class="col-12s col-md-4 text-end">
                    <!-- data-bs-toggle="modal" data-bs-target="#trackCodesModal" -->
                    <button type="button" class="btn btn-primary btn-lg" wire:click="getTrackCodes('{{ $group['group']->pluck('id') }}')">Open</button>
                    <button type="button" class="btn btn-success btn-lg" wire:click="toSend('{{ $group['group']->pluck('id') }}')">Send</button>
                  </div>
                </div>
              </div>
            </div>
          @endif
        @endforeach

      </div>
    </div>

  </main>

  <!-- Modal -->
  <div class="modal fade" id="trackCodesModal" tabindex="-1" aria-labelledby="trackCodesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="trackCodesModalLabel">Track codes</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @foreach($trackCodes as $trackCode)
            <div><b>Track code:</b> {{ $trackCode->code }}</div>
          @endforeach
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
