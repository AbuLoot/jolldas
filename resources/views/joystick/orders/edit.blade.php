@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/orders" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
  </p>
  <div class="row">
    <div class="col-md-7">
      <div class="panel panel-default">
        <div class="panel-body">
          <form action="{{ route('orders.update', [$lang, $order->id]) }}" method="post">
            <input type="hidden" name="_method" value="PUT">
            {!! csrf_field() !!}

            <div class="form-group">
              <label for="name">Имя:</label>
              <input type="text" class="form-control" name="name" id="name" minlength="2" maxlength="60" value="{{ $order->name }}" required>
            </div>
            <div class="form-group">
              <label for="phone">Номера телефона</label>
              <input type="text" class="form-control" id="phone" name="phone" value="{{ (old('phone')) ? old('phone') : $order->phone }}">
            </div>
            <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" class="form-control" name="email" id="email" minlength="8" maxlength="60" value="{{ $order->email }}">
            </div>
            <div class="form-group">
              <label for="company_name">Название компаний</label>
              <textarea class="form-control" id="company_name" name="company_name" rows="5">{{ (old('company_name')) ? old('company_name') : $order->company_name }}</textarea>
            </div>
            <div class="form-group">
              <label for="data_1">Данные 1</label>
              <input type="text" class="form-control" id="data_1" name="data_1" value="{{ (old('data_1')) ? old('data_1') : $order->data_1 }}">
            </div>
            <div class="form-group">
              <label for="data_2">Данные 2</label>
              <input type="text" class="form-control" id="data_2" name="data_2" value="{{ (old('data_2')) ? old('data_2') : $order->data_2 }}">
            </div>
            <div class="form-group">
              <label for="data_3">Данные 3</label>
              <input type="text" class="form-control" id="data_3" name="data_3" value="{{ (old('data_3')) ? old('data_3') : $order->data_3 }}">
            </div>
            <div class="form-group">
              <label for="countries">Страны</label>
              <select id="region_id" name="region_id" class="form-control">
                <option value=""></option>
                <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $order) { ?>
                  <?php foreach ($nodes as $node) : ?>
                    <option value="{{ $node->id }}" @if($node->id == $order->region_id) selected @endif>{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                    <?php $traverse($node->children, $prefix.'___'); ?>
                  <?php endforeach; ?>
                <?php }; ?>
                <?php $traverse($regions); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="legal_address">Юридический адрес</label>
              <input type="text" class="form-control" id="legal_address" name="legal_address" value="{{ (old('legal_address')) ? old('legal_address') : $order->legal_address }}">
            </div>
            <div class="form-group">
              <label for="address">Адрес</label>
              <input type="text" class="form-control" id="address" name="address" value="{{ (old('address')) ? old('address') : $order->address }}">
            </div>
            <div class="form-group">
              <label for="count">Количество товаров</label><br>
              <?php
                $countAllProducts = unserialize($order->count);
                $i = 0;
                $c = 0;
              ?>
              <table class="table">
                <tbody>
                  @foreach ($countAllProducts as $id => $countInfo)
                    <tr>
                      <td>
                        @if($order->products[$i]->id == $id)
                          <img src="/img/products/{{ $order->products[$i]->path.'/'.$order->products[$i]->image }}" style="width:80px;height:80px;">
                          {{ $countInfo['quantity'] . ' шт. ' }} <a href="/p/{{ $order->products[$i]->id.'-'.$order->products[$i]->slug }}" target="_blank">{{ $order->products[$i]->title }}</a><br><br>
                        @endif
                        <?php $c += $countInfo['quantity']; ?>
                      </td>
                      <td>
                        <?php $idCodes = json_decode($order->products[$i]->id_codes, true) ?? ['']; ?>
                        <label for="id_codes">Артикулы</label>
                        <select id="id_codes" name="id_codes[]" class="form-control" required>
                          <option value="">Выберите артикул</option>
                          @foreach($idCodes as $idCode => $idCodeCount)
                            <option value="{{ $idCode }}" @if($countInfo['id_code'] == $idCode || count($idCodes) == 1) selected @endif>{{ $idCode.' '.$idCodeCount }}шт</option>
                          @endforeach
                        </select>
                      </td>
                      <?php $i++; ?>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <p>Общее количество товаров: {{ $c }} шт.</p>
            </div>
            <div class="form-group">

            </div>
            <div class="form-group">
              <label for="price">Цена</label>
              <input type="text" class="form-control" id="price" name="price" value="{{ (old('price')) ? old('price') : $order->price }}〒">
            </div>
            <div class="form-group">
              <label for="amount">Сумма</label>
              <input type="text" class="form-control" id="amount" name="amount" value="{{ (old('amount')) ? old('amount') : $order->amount }}〒">
            </div>
            <div class="form-group">
              <label for="delivery">Способ доставки:</label>
              <select id="delivery" name="delivery" class="form-control">
                <option value="0"></option>
                @foreach(trans('orders.get') as $key => $value)
                  @if ($key == $order->delivery)
                    <option value="{{ $key }}" selected>{{ $value['value'] }}</option>
                  @else
                    <option value="{{ $key }}">{{ $value['value'] }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="payment_type">Способ оплаты:</label>
              <select id="payment_type" name="payment_type" class="form-control">
                <option value="0"></option>
                @foreach(trans('orders.pay') as $key => $value)
                  @if ($key == $order->payment_type)
                    <option value="{{ $key }}" selected>{{ $value['value'] }}</option>
                  @else
                    <option value="{{ $key }}">{{ $value['value'] }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="status">Статус:</label>
              <select id="status" name="status" class="form-control" required>
                <option value="0"></option>
                @foreach(trans('orders.statuses') as $key => $title)
                  @if ($key == $order->status)
                    <option value="{{ $key }}" selected>{{ $title }}</option>
                  @else
                    <option value="{{ $key }}">{{ $title }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-success"><i class="material-icons">save</i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

@endsection
