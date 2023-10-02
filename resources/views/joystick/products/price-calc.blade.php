@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/products" class="btn btn-primary btn-sm">Назад</a>
  </p>
  <form action="/{{ $lang }}/admin/products-price/update" method="POST">
    {!! csrf_field() !!}

    <div class="row">
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">Калькулятор</div>
          <div class="panel-body">
            <div class="form-group">
              <label for="category_id">Категории</label>
              <select id="category_id" name="category_id" class="form-control" required>
                <option value=""></option>
                <?php $traverse = function ($nodes, $prefix = null) use (&$traverse) { ?>
                  <?php foreach ($nodes as $node) : ?>
                    <option value="{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                    <?php $traverse($node->children, $prefix.'___'); ?>
                  <?php endforeach; ?>
                <?php }; ?>
                <?php $traverse($categories); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="operation">Оператор</label>
              <select class="form-control" name="operation" id="operation">
                <option value="*">* умножение</option>
                <option value="/">/ деление</option>
                <option value="+">+ плюс</option>
                <option value="-">- минус</option>
              </select>
            </div>
            <div class="form-group">
              <label for="number">Цифра</label>
              <input type="text" class="form-control" id="number" name="number" maxlength="80" value="{{ (old('number')) ? old('number') : '' }}" required>
            </div>
            <div class="form-group">
              <label><input type="radio" id="round" name="round" value="ceil"> Округление на увеличения</label>
              <label><input type="radio" id="round" name="round" value="round"> Округление на ближающию</label>
              <label><input type="radio" id="round" name="round" value="floor"> Округление на уменьшения</label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-primary">Обновить</button>
    </div>
  </form>
@endsection
