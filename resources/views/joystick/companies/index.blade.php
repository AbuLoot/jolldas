@extends('joystick.layout')

@section('content')

  <h2 class="page-header">Компании</h2>

  @include('components.alerts')

  <div class="text-right">
    <div class="btn-group">
      <button type="button" id="submit" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Функции <span class="caret"></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-right" id="actions">
        @foreach(trans('statuses.data') as $num => $status)
          <li><a data-action="{{ $num }}" href="#">Статус {{ $status['title'] }}</a></li>
        @endforeach
        <li role="separator" class="divider"></li>
        <li><a data-action="destroy" href="#" onclick="return confirm('Удалить записи?')">Удалить</a></li>
      </ul>
    </div>
    <a href="/{{ $lang }}/admin/companies/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </div><br>
  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td><input type="checkbox" onclick="toggleCheckbox(this)" class="checkbox-ids"></td>
          <td>№</td>
          <td>Картинка</td>
          <td>Название</td>
          <td>Номер</td>
          <td>Поставщик</td>
          <td>Заказчик</td>
          <td>Статус</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        @foreach ($companies as $company)
          <tr>
            <td><input type="checkbox" name="companies_id[]" value="{{ $company->id }}" class="checkbox-ids"></td>
            <td>{{ $i++ }}</td>
            <td><img src="/img/companies/{{ $company->image }}" class="img-responsive" style="width:80px;"></td>
            <td>{{ $company->title }}</td>
            <td>{{ $company->sort_id }}</td>
            <td class="text-info">{{ trans('statuses.data.'.$company->is_supplier.'.title') }}</td>
            <td class="text-info">{{ trans('statuses.data.'.$company->is_customer.'.title') }}</td>
            <td class="text-info">{{ trans('statuses.data.'.$company->status.'.title') }}</td>
            <td class="text-right">
              <a class="btn btn-link btn-xs" href="{{ route('companies.edit', [$lang, $company->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form method="POST" action="{{ route('companies.destroy', [$lang, $company->id]) }}" accept-charset="UTF-8" class="btn-delete">
                <input name="_method" type="hidden" value="DELETE">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $companies->links() }}

@endsection

@section('scripts')
  <script>
    // Submit button click
    $("#actions > li > a").click(function() {

      var action = $(this).data("action");
      var companiesId = new Array();

      $('input[name="companies_id[]"]:checked').each(function() {
        companiesId.push($(this).val());
      });

      if (companiesId.length > 0) {
        $.ajax({
          type: "get",
          url: '/{{ $lang }}/admin/companies-actions',
          dataType: "json",
          data: {
            "action": action,
            "companies_id": companiesId
          },
          success: function(data) {
            console.log(data);
            location.reload();
          }
        });
      }
    });

    // Toggle checkbox
    function toggleCheckbox(source) {
      var checkboxes = document.querySelectorAll('input[type="checkbox"]');
      for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source)
          checkboxes[i].checked = source.checked;
      }
    }
  </script>
@endsection