<div>

  <div wire:ignore.self class="modal fade" id="modalEditTrack" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form wire:submit.prevent="saveTrack">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="modalLabel">Редактирование трека</h1>
            <button type="button" id="closeEditTrack" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="code" class="form-label">Трек номер</label>              
              @if(isset($track) && $track->status > 1)
                <input type="text" class="form-control form-control-lg" id="code" value="{{ $track->code }}" disabled>
              @else
                <input wire:model.defer="track.code" type="text" class="form-control form-control-lg @error('track.code') is-invalid @enderror" id="code">
              @endif
              @error('track.code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Описание</label>
              <textarea wire:model.defer="track.description" class="form-control form-control-lg @error('track.description') is-invalid @enderror" id="description"></textarea>
              @error('track.description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-lg">Сохранить</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
