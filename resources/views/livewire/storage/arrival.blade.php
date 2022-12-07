<div>

  <div class="px-3 py-3 border-bottom mb-3">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">

      <h4 class="col-12 col-lg-4 mb-md-2 mb-lg-0">Track codes group</h4>

      <form class="col-12 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto">
        <input type="search" class="form-control form-control-lg" placeholder="Enter track code..." aria-label="Search">
      </form>

    </div>
  </div>

  <div class="container">

    <div class="row">
      <div class="col-12 col-sm-12">
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
          $todayGroup         = $tracksGroup->where('updated_at', '>', $yesterday.' 23:59:59')->where('updated_at', '<=', now());
          $yesterdayGroup     = $tracksGroup->where('updated_at', '>', $yesterday)->where('updated_at', '<', $today);
          $twoDaysAgoGroup    = $tracksGroup->where('updated_at', '>', $twoDaysAgo)->where('updated_at', '<', $yesterday);
          $threeDaysAgoGroup  = $tracksGroup->where('updated_at', '>', $threeDaysAgo)->where('updated_at', '<', $twoDaysAgo);
          $fourDaysAgoGroup   = $tracksGroup->where('updated_at', '>', $fourDaysAgo)->where('updated_at', '<', $threeDaysAgo);
          $fiveDaysAgoGroup   = $tracksGroup->where('updated_at', '>', $fiveDaysAgo)->where('updated_at', '<', $fourDaysAgo);
          $sixDaysAgoGroup    = $tracksGroup->where('updated_at', '>', $sixDaysAgo)->where('updated_at', '<', $fiveDaysAgo);
          $previousWeekGroup  = $tracksGroup->where('updated_at', '>', $previousWeek)->where('updated_at', '<', $sixDaysAgo);
          $twoWeekAgoGroup    = $tracksGroup->where('updated_at', '>', $twoWeekAgo)->where('updated_at', '<', $previousWeek);
          $prevTimeGroup      = $tracksGroup->where('updated_at', '<', $twoWeekAgo);

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

        @foreach($allTracksGroups as $group)
          @if($group['group']->count())
            <div class="tracks-group mb-2">
              <div class="border bg-sent rounded p-2">
                <div class="row">
                  <div class="col-6 col-md-4">
                    <div><b>Date:</b> {{ $group['date'] }}</div>
                    <div><b>Count:</b> {{ $group['group']->count() }}pcs</div>
                  </div>
                  <div class="col-6 col-md-4"><b>Sent: {{ $group['dateName'] }}</b></div>
                  <div class="col-12s col-md-4 text-end">
                    <button type="button" class="btn btn-primary btn-lg" wire:click="getTrackCodes('{{ $group['group']->pluck('id') }}')">Open</button>
                    <button type="button" class="btn btn-success btn-lg" wire:click="toAccept('{{ $group['group']->pluck('id') }}')">Arrived</button>
                  </div>
                </div>
              </div>
            </div>
          @endif
        @endforeach

      </div>
    </div>

  </div>

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
      var docModal = new bootstrap.Modal(document.getElementById("trackCodesModal"), {});
      docModal.show();
    })
  </script>
</div>
