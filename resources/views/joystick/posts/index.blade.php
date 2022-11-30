@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Статьи</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/posts/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </p>
  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Название</td>
          <td>URI</td>
          <td>Заголовок</td>
          <td>Номер</td>
          <td>Язык</td>
          <td>Статус</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        @foreach ($posts as $post)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $post->title }}</td>
            <td>{{ $post->slug }}</td>
            <td>{{ $post->headline }}</td>
            <td>{{ $post->sort_id }}</td>
            <td>{{ $post->lang }}</td>
            @if ($post->status == 1)
              <td class="text-success">Активен</td>
            @else
              <td class="text-danger">Неактивен</td>
            @endif
            <td class="text-right text-nowrap">
              <a class="btn btn-link btn-xs" href="{{ route('posts.edit', [$lang, $post->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form method="POST" action="{{ route('posts.destroy', [$lang, $post->id]) }}" accept-charset="UTF-8" class="btn-delete">
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

  {{ $posts->links() }}

@endsection