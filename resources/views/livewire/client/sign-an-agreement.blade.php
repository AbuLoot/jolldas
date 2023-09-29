<div>

  <div wire:ignore.self class="modal fade" id="modalAgreement" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Подписание договора</h1>
        </div>
        <div class="modal-body">
          <?php echo nl2br($agreement->content); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отложить</button>
          <button wire:click="toSign" type="button" class="btn btn-primary">Подписать</button>
        </div>
      </div>
    </div>
  </div>

</div>

@section('scripts')
  <script type="text/javascript">
    const myModal = new bootstrap.Modal(document.getElementById('modalAgreement'));
    myModal.show()
  </script>
@endsection