
      <!-- <div class="col-4 col-lg-4 mb-md-2 mb-lg-0 me-lg-auto text-end">
        <button type="button" class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#filters" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Фильтр"><i class="bi bi-funnel-fill"></i> <span class="d-none d-sm-inline">Filters</span></button>
      </div> -->

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