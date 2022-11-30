@extends('joystick.layout')

@section('head')

@endsection

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')
  <p class="text-right">
    <a href="/{{ $lang }}/admin/sections" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
  </p>
  <div class="panel panel-default">
    <div class="panel-body">
      <form action="{{ route('sections.update', [$lang, $section->id]) }}" method="post">
        <input type="hidden" name="_method" value="PUT">
        {!! csrf_field() !!}

        <div class="form-group">
          <label for="title">Заголовок сервиса</label>
          <input type="text" class="form-control" id="title" name="title" minlength="2" maxlength="80" value="{{ (old('title')) ? old('title') : $section->title }}" required>
        </div>
        <div class="form-group">
          <label for="slug">Slug</label>
          <input type="text" class="form-control" id="slug" name="slug" minlength="2" maxlength="80" value="{{ (old('slug')) ? old('slug') : $section->slug }}">
        </div>
        <div class="form-group">
          <label for="sort_id">Номер</label>
          <input type="text" class="form-control" id="sort_id" name="sort_id" maxlength="5" value="{{ (old('sort_id')) ? old('sort_id') : $section->sort_id }}">
        </div>
        <div class="form-group">
          <label for="meta_title">Мета название</label>
          <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="255" value="{{ (old('meta_title')) ? old('meta_title') : $section->meta_title }}">
        </div>
        <div class="form-group">
          <label for="meta_description">Мета описание</label>
          <input type="text" class="form-control" id="meta_description" name="meta_description" maxlength="255" value="{{ (old('meta_description')) ? old('meta_description') : $section->meta_description }}">
        </div>
        <?php $data_1 = unserialize($section->data_1); ?>
        <div class="form-group row">
          <div class="col-md-3">
            <label for="data_1_key">Название</label>
            <input type="text" class="form-control" id="data_1_key" name="data[key][]" maxlength="255" value="{{ $data_1['key'] }}">
          </div>
          <div class="col-md-5">
            <label for="data_1_value">Значение - чтобы разделить значения используйте знак /</label>
            <input type="text" class="form-control" id="data_1_value" name="data[value][]" maxlength="255" value="{{ $data_1['value'] }}">
          </div>
        </div>
        <?php $data_2 = unserialize($section->data_2); ?>
        <div class="form-group row">
          <div class="col-md-3">
            <label for="data_2_key">Название</label>
            <input type="text" class="form-control" id="data_2_key" name="data[key][]" maxlength="255" value="{{ $data_2['key'] }}">
          </div>
          <div class="col-md-5">
            <label for="data_2_value">Значение - чтобы разделить значения используйте знак /</label>
            <input type="text" class="form-control" id="data_2_value" name="data[value][]" maxlength="255" value="{{ $data_2['value'] }}">
          </div>
        </div>
        <?php $data_3 = unserialize($section->data_3); ?>
        <div class="form-group row">
          <div class="col-md-3">
            <label for="data_3_key">Название</label>
            <input type="text" class="form-control" id="data_3_key" name="data[key][]" maxlength="255" value="{{ $data_3['key'] }}">
          </div>
          <div class="col-md-5">
            <label for="data_3_value">Значение - чтобы разделить значения используйте знак /</label>
            <input type="text" class="form-control" id="data_3_value" name="data[value][]" maxlength="255" value="{{ $data_3['value'] }}">
          </div>
        </div>
        <div class="form-group">
          <label for="content">Контент</label>
          <textarea class="form-control" id="content" name="content" rows="10">{{ (old('content')) ? old('content') : $section->content }}</textarea>
        </div>
        <div class="form-group">
          <label for="lang">Язык</label>
          <select id="lang" name="lang" class="form-control" required>
            <option value=""></option>
            @foreach($languages as $language)
              @if ($section->lang == $language->slug)
                <option value="{{ $language->slug }}" selected>{{ $language->title }}</option>
              @else
                <option value="{{ $language->slug }}">{{ $language->title }}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="status">Статус</label>
          <label>
            @if ($section->status == 1)
              <input type="checkbox" id="status" name="status" checked> Активен
            @else
              <input type="checkbox" id="status" name="status"> Активен
            @endif
          </label>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-success"><i class="material-icons">save</i></button>
        </div>
      </form>
    </div>
  </div>
@endsection

@section('scripts')

@endsection