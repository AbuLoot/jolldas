@extends('joystick.layout')

@section('content')

  <h2 class="page-header">Регионы</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/regions/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </p>
  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Название</td>
          <td>Slug</td>
          <td>Номер</td>
          <td>Язык</td>
          <td>Статус</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        <?php $traverse = function ($nodes, $parent = null, $prefix = null, $caret = null) use (&$traverse, $lang, &$i) { ?>
          <?php foreach ($nodes as $node) : ?>
            <tr <?php if ($parent != null): $classes = $node->ancestors->pluck('id')->flatten()->join(' '); ?> class="collapse {{ $classes }} in" <?php endif; ?>>
              <td>{{ $i++ }}</td>
              <td
                <?php if ($node->descendants->count() > 0): $caret = '<span class="caret"></span>'; ?>
                  class="node-title" data-toggle="collapse" data-target=".{{ $node->id }}" aria-expanded="false" aria-controls="{{ $node->id }}"
                <?php endif; ?>>
                {!! $caret !!} {{ PHP_EOL.$prefix.' '.$node->title }} <?php $caret = null; ?>
              </td>
              <td>{{ $node->slug }}</td>
              <td>{{ $node->sort_id }}</td>
              <td>{{ $node->lang }}</td>
              <td class="text-{{ trans('statuses.data.'.$node->status.'.style') }}">{{ trans('statuses.data.'.$node->status.'.title') }}</td>
              <td class="text-right">
                <a class="btn btn-link btn-xs" href="{{ route('regions.edit', [$lang, $node->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
                <form method="POST" action="{{ route('regions.destroy', [$lang, $node->id]) }}" accept-charset="UTF-8" class="btn-delete">
                  <input name="_method" type="hidden" value="DELETE">
                  <input name="_token" type="hidden" value="{{ csrf_token() }}">
                  <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
                </form>
              </td>
            </tr>
            <?php $traverse($node->children, $node, $prefix.'____'); ?>
          <?php endforeach; ?>
        <?php }; ?>
        <?php $traverse($regions); ?>
      </tbody>
    </table>
  </div>

  {{-- $regions->links() --}}

@endsection