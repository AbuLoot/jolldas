@extends('joystick.layout')

@section('head')

@endsection

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')
  <p class="text-right">
    <a href="/{{ $lang }}/admin/currencies" class="btn btn-primary"><i class="material-icons">arrow_back</i></a>
  </p>
  <div class="row">
    <div class="col-md-7">
      <div class="panel panel-default">
        <div class="panel-body">
          <form action="{{ route('currencies.update', [$lang, $currency->id]) }}" method="post">
            <input type="hidden" name="_method" value="PUT">
            {!! csrf_field() !!}

            <div class="form-group">
              <label for="sort_id">Номер</label>
              <input type="text" class="form-control" id="sort_id" name="sort_id" value="{{ (old('sort_id')) ? old('sort_id') : $currency->sort_id }}">
            </div>
            <div class="form-group">
              <label for="currency">Валюта</label>
              <input type="text" class="form-control" id="currency" name="currency" minlength="2" maxlength="80" value="{{ (old('currency')) ? old('currency') : $currency->currency }}" required>
            </div>
            <div class="form-group">
              <label for="country">Страна</label>
              <input type="text" class="form-control" id="country" name="country" minlength="2" maxlength="80" value="{{ (old('country')) ? old('country') : $currency->country }}">
            </div>
            <div class="form-group">
              <label for="code">Код</label>
              <input type="text" class="form-control" id="code" name="code" maxlength="10" value="{{ (old('code')) ? old('code') : $currency->code }}">
            </div>
            <div class="form-group">
              <label for="symbol">Символ</label>
              <input type="text" class="form-control" id="symbol" name="symbol" maxlength="10" value="{{ (old('symbol')) ? old('symbol') : $currency->symbol }}">
            </div>
            <div class="form-group">
              <label for="lang">Язык</label>
              <input type="text" class="form-control" id="lang" name="lang" maxlength="10" value="{{ (old('lang')) ? old('lang') : $currency->lang }}">
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

@section('scripts')

@endsection