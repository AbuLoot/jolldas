<ul class="dropdown-menu dropdown-menu-items">
  @foreach($users as $user)
    <li><a href="/{{ $lang }}/admin/tracks/{{ $trackId }}/pin-user/{{ $user->id }}"><b>{{ 'ID: '.$user->id_client.'. '.$user->name.' '.$user->lastname }}</b></a></li>
  @endforeach
</ul>