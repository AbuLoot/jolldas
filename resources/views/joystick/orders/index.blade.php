@extends('joystick.layout')

@section('content')

  <h2 class="page-header">Заказы</h2>

  @include('components.alerts')
  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Дата</td>
          <td>Заказчик</td>
          <td>Телефон</td>
          <td>Email</td>
          <td>Город</td>
          <td>Количество</td>
          <td>Сумма</td>
          <td>Статус</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        @foreach ($orders as $order)
          <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->created_at }}</td>
            <td>{{ $order->name }}</td>
            <td>{{ $order->phone }}</td>
            <td>{{ $order->email }}</td>
            <td>{{ (isset($order->region->title)) ? $order->region->title : '' }} {{ $order->address }}</td>
            <td>
              <?php $countAllProducts = unserialize($order->count); $i = 0; ?>
              @foreach ($countAllProducts as $id => $countInfo)
                @if (isset($order->products[$i]) AND $order->products[$i]->id == $id)
                  {{ $countInfo['quantity'] . ' шт. ' . $order->products[$i]->title  }}<br>
                @endif
                <?php $i++; ?>
              @endforeach
            </td>
            <td class="text-nowrap">{{ $order->amount }}〒</td>
            <td>{{ trans('orders.statuses.'.$order->status) }}</td>
            <td class="text-right">
              <a class="btn btn-link btn-xs" href="{{ route('orders.edit', [$lang, $order->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form method="POST" action="{{ route('orders.destroy', [$lang, $order->id]) }}" accept-charset="UTF-8" class="btn-delete">
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

  {{ $orders->links() }}

@endsection