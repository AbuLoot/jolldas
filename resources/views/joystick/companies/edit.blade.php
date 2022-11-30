@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/companies" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
  </p>
  <div class="row">
    <div class="col-md-7">
      <div class="panel panel-default">
        <div class="panel-body">
          <form action="{{ route('companies.update', [$lang, $company->id]) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="PUT">
            {!! csrf_field() !!}

            <div class="form-group">
              <label for="title">Название</label>
              <input type="text" class="form-control" id="title" name="title" minlength="2" maxlength="80" value="{{ (old('title')) ? old('title') : $company->title }}" required>
            </div>
            <div class="form-group">
              <label for="slug">Slug</label>
              <input type="text" class="form-control" id="slug" name="slug" minlength="2" maxlength="80" value="{{ (old('slug')) ? old('slug') : $company->slug }}">
            </div>
            <div class="form-group">
              <label for="bin">БИН</label>
              <input type="text" class="form-control" id="bin" name="bin" value="{{ (old('bin')) ? old('bin') : $company->bin }}">
            </div>
            <div class="form-group">
              <label for="sort_id">Номер</label>
              <input type="text" class="form-control" id="sort_id" name="sort_id" value="{{ (old('sort_id')) ? old('sort_id') : $company->sort_id }}">
            </div>
            <div class="form-group">
              <label for="region_id">Регионы</label>
              <select id="region_id" name="region_id" class="form-control">
                <option value=""></option>
                <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $company) { ?>
                  <?php foreach ($nodes as $node) : ?>
                    <option value="{{ $node->id }}" <?= ($node->id == $company->region_id) ? 'selected' : ''; ?>>{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                    <?php $traverse($node->children, $prefix.'___'); ?>s
                  <?php endforeach; ?>
                <?php }; ?>
                <?php $traverse($regions); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="currency_id">Валюты</label>
              <select id="currency_id" name="currency_id" class="form-control">
                <option value=""></option>
                @foreach ($currencies as $currency)
                  <option value="{{ $currency->id }}" @if($company->currency_id == $currency->id) selected @endif>{{ $currency->symbol }} - {{ $currency->currency }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="legal_address">Юридический адрес</label>
              <input type="text" class="form-control" id="legal_address" name="legal_address" value="{{ (old('legal_address')) ? old('legal_address') : $company->legal_address }}">
            </div>
            <div class="form-group">
              <label for="actual_address">Фактический адрес</label>
              <input type="text" class="form-control" id="actual_address" name="actual_address" value="{{ (old('actual_address')) ? old('actual_address') : $company->actual_address }}">
            </div>
            <div class="form-group">
              <label for="image">Логотип</label><br>
              <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width:300px;height:200px;">
                  <img src="/img/companies/{{ $company->image }}">
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail" style="width:300px;height:200px;"></div>
                <div>
                  <span class="btn btn-default btn-sm btn-file">
                    <span class="fileinput-new"><i class="glyphicon glyphicon-folder-open"></i>&nbsp; Выбрать</span>
                    <span class="fileinput-exists"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;</span>
                    <input type="file" name="image" accept="image/*">
                  </span>
                  <a href="#" class="btn btn-default btn-sm fileinput-exists" data-dismiss="fileinput"><i class="glyphicon glyphicon-trash"></i> Удалить</a>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="about">О компании</label>
              <textarea class="form-control" id="about" name="about" rows="5">{{ (old('about')) ? old('about') : $company->about }}</textarea>
            </div>
            <div class="form-group">
              <label for="phones">Номера телефонов</label>
              <input type="text" class="form-control" id="phones" name="phones" value="{{ (old('phones')) ? old('phones') : $company->phones }}">
            </div>
            <div class="form-group">
              <label for="links">Website</label>
              <input type="text" class="form-control" id="links" name="links" value="{{ (old('links')) ? old('links') : $company->links }}">
            </div>
            <div class="form-group">
              <label for="emails">Emails</label>
              <input type="text" class="form-control" id="emails" name="emails" value="{{ (old('emails')) ? old('emails') : $company->emails }}">
            </div>
            <div class="form-group">
              <label for="is_supplier">Поставщик:</label>
              <label>
                <input type="checkbox" id="is_supplier" name="is_supplier" @if($company->is_supplier == 1) checked @endif> Активен
              </label>
            </div>
            <div class="form-group">
              <label for="is_customer">Заказщик:</label>
              <label>
                <input type="checkbox" id="is_customer" name="is_customer" @if($company->is_customer == 1) checked @endif> Активен
              </label>
            </div>
            <div class="form-group">
              <label for="status">Статус:</label>
              <label>
                <input type="checkbox" id="status" name="status" @if ($company->status == 1) checked @endif> Активен
              </label>
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

@section('head')
  <link href="/joystick/css/jasny-bootstrap.min.css" rel="stylesheet">
@endsection

@section('scripts')
  <script src="/joystick/js/jasny-bootstrap.js"></script>
@endsection

