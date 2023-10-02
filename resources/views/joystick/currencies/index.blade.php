@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Валюты</h2>

  @include('components.alerts')
  <p class="text-right">
    <a href="/{{ $lang }}/admin/currencies/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
  </p>
  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Символ</td>
          <td>Название валюты</td>
          <td>Номер</td>
          <td>Страна</td>
          <td>Код</td>
          <td>Язык</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        @foreach ($currencies as $currency)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $currency->symbol }}</td>
            <td>{{ $currency->currency }}</td>
            <td>{{ $currency->sort_id }}</td>
            <td>{{ $currency->country }}</td>
            <td>{{ $currency->code }}</td>
            <td>{{ $currency->lang }}</td>
            <td class="text-right text-nowrap">
              <a class="btn btn-link btn-xs" href="{{ route('currencies.edit', [$lang, $currency->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form class="btn-delete" method="POST" action="{{ route('currencies.destroy', [$lang, $currency->id]) }}" accept-charset="UTF-8">
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
@endsection