@extends('joystick.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('components.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/users/password/{{ $user->id }}/edit" class="btn btn-default">Изменить пароль</a>
    <a href="/{{ $lang }}/admin/users" class="btn btn-primary"><i class="material-icons md-18">arrow_back</i></a>
  </p>

  <form action="{{ route('users.update', [$lang, $user->id]) }}" method="post" enctype="multipart/form-data">
    <input name="_method" type="hidden" value="PUT">
    {!! csrf_field() !!}

    <div class="row">
      <div class="col-md-7">
        <div class="panel panel-default">
          <div class="panel-heading">Основная информация</div>
          <div class="panel-body">
            <div class="row">
              <div class="col-6 col-md-6">
                <div class="form-group">
                  <label>Имя</label>
                  <input type="text" class="form-control" minlength="2" maxlength="40" name="name" placeholder="Имя*" value="{{ (old('name')) ? old('name') : $user->name }}" required>
                </div>
              </div>
              <div class="col-6 col-md-6">
                <div class="form-group">
                  <label>Отчество</label>
                  <input type="text" class="form-control" minlength="2" maxlength="60" name="lastname" placeholder="Отчество*" value="{{ (old('lastname')) ? old('lastname') : $user->lastname }}">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" class="form-control" name="email" id="email" minlength="8" maxlength="60" value="{{ $user->email }}">
            </div>
            <div class="form-group">
              <label>Номер телефона</label>
              <input type="tel" pattern="(\+?\d[- .]*){7,13}" class="form-control" name="tel" placeholder="Номер телефона*" value="{{ (old('tel')) ? old('tel') : $user->tel }}">
            </div>
            <div class="form-group">
              <label>ID client</label>
              <input type="text" class="form-control" name="id_client" maxlength="30" placeholder="ID client*" value="{{ (old('id_client')) ? old('id_client') : $user->id_client }}">
            </div>
            <div class="form-group">
              <label>ID name</label>
              <input type="text" class="form-control" name="id_name" maxlength="30" placeholder="ID name*" value="{{ (old('id_name')) ? old('id_name') : $user->id_name }}">
            </div>
            <div class="form-group">
              <label>Адрес</label>
              <input type="text" class="form-control" name="address" placeholder="Адрес*" value="{{ (old('address')) ? old('address') : $user->address }}">
            </div>
            <div class="form-group">
              <label for="role_id">Роли:</label>
              <select class="form-control" name="role_id" id="role_id">
                <option value=""></option>
                @foreach($roles as $role)
                  @if ($user->roles->contains($role->id)))
                    <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
                  @else
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="is_customer">Клиент:</label>
              <label>
                <input type="checkbox" id="is_customer" name="is_customer" @if($user->is_customer == 1) checked @endif> Активен
              </label>
            </div>
            <div class="form-group">
              <label for="is_worker">Сотрудник:</label>
              <label>
                <input type="checkbox" id="is_worker" name="is_worker" @if($user->is_worker == 1) checked @endif> Активен
              </label>
            </div>
            <div class="form-group">
              <label for="status">Статус:</label>
              <label>
                <input type="checkbox" id="status" name="status" @if($user->status == 1) checked @endif> Активен
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">Профиль</div>
          <div class="panel-body">
            <div class="form-group">
              <label>Регион</label>
              <select id="region_id" name="region_id" class="form-control">
                <option value=""></option>
                <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $user) { ?>
                  <?php foreach ($nodes as $node) : ?>
                    <option value="{{ $node->id }}" <?= ($node->id == $user->region_id) ? 'selected' : ''; ?>>{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                    <?php $traverse($node->children, $prefix.'___'); ?>
                  <?php endforeach; ?>
                <?php }; ?>
                <?php $traverse($regions); ?>
              </select>
            </div>
            <div class="form-group">
              <label>Компании</label>
              <select id="company_id" name="company_id" class="form-control">
                <option value=""></option>
                <?php foreach ($companies as $company) : ?>
                  <option value="{{ $company->id }}" <?= ($company->id == $user->profile->company_id) ? 'selected' : ''; ?>>{{ $company->title }}</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Дата рождения</label>
              <input type="date" class="form-control" name="birthday" minlength="3" maxlength="30" placeholder="Дата рождения" value="{{ (old('birthday')) ? old('birthday') : $user->profile->birthday }}" >
            </div>
            <div class="form-group">
              <div><label>Пол</label></div>
              @foreach(trans('data.gender') as $key => $value)
                <label>
                  <input type="radio" name="gender" @if($key == $user->profile->gender) checked @endif value="{{ $key }}"> {{ $value }}
                </label>
              @endforeach
            </div>
            <div class="form-group">
              <label for="about">О себе</label>
              <textarea class="form-control" id="about" name="about" rows="5">{{ (old('about')) ? old('about') : $user->profile->about }}</textarea>
            </div>
            <div class="form-group">
              <label for="is_debtor">Должник:</label>
              <input type="checkbox" id="is_debtor" name="is_debtor" @if($user->profile->is_debtor == 1) checked @endif disabled> Активен
            </div>
            <div class="form-group">
              <label>Сумма долга</label>
              <input type="number" class="form-control" name="debt_sum" maxlength="30" placeholder="Сумма долга" value="{{ (old('debt_sum')) ? old('debt_sum') : $user->profile->debt_sum }}">
            </div>
            <div class="form-group">
              <label>Бонус</label>
              <input type="number" class="form-control" name="bonus" maxlength="30" placeholder="Бонус" value="{{ (old('bonus')) ? old('bonus') : $user->profile->bonus }}">
            </div>
            <div class="form-group">
              <label>Скидка</label>
              <input type="number" class="form-control" name="discount" minlength="0" maxlength="10" placeholder="Скидка" value="{{ (old('discount')) ? old('discount') : $user->profile->discount }}">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-success"><i class="material-icons">save</i></button>
    </div>
  </form>
@endsection
