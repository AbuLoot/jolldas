@extends('joystick.layout')

@section('content')

  <h2 class="page-header">Индексы проектов</h2>

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
      </ul>
    </div>
    <a href="/{{ $lang }}/admin/projects-indexing" class="btn btn-primary"><i class="material-icons md-18">list</i> Индексировать</a>
    <a href="/{{ $lang }}/admin/projects-index/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </div><br>
  <div class="table-responsive">
    <table class="table table-condensed table-hover">
      <thead>
        <tr class="active">
          <td><input type="checkbox" onclick="toggleCheckbox(this)" class="checkbox-ids"></td>
          <td>№</td>
          <td>Оригинал</td>
          <td>Название</td>
          <td>Язык</td>
          <td>Статус</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($projects_index as $project_index) : ?>
          <tr>
            <td><input type="checkbox" name="projects_index_id[]" value="{{ $project_index->id }}" class="checkbox-ids"></td>
            <td>{{ $project_index->sort_id }}</td>
            <td>{{ $project_index->original }}</td>
            <td>{{ $project_index->title }}</td>
            <td>{{ $project_index->lang }}</td>
            <td class="text-{{ trans('statuses.data.'.$project_index->status.'.style') }}">{{ trans('statuses.data.'.$project_index->status.'.title') }}</td>
            <td class="text-right">
              <a class="btn btn-link btn-xs" href="{{ route('projects-index.edit', [$lang, $project_index->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form method="POST" action="{{ route('projects-index.destroy', [$lang, $project_index->id]) }}" accept-charset="UTF-8" class="btn-delete">
                <input name="_method" type="hidden" value="DELETE">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
@endsection

@section('scripts')
  <script>
    // Submit button click
    $("#actions > li > a").click(function() {

      var action = $(this).data("action");
      var projectsIndexId = new Array();

      $('input[name="projects_index_id[]"]:checked').each(function() {
        projectsIndexId.push($(this).val());
      });

      if (projectsIndexId.length > 0) {
        $.ajax({
          type: "get",
          url: '/{{ $lang }}/admin/projects-index-actions',
          dataType: "json",
          data: {
            "action": action,
            "projects_index_id": projectsIndexId
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