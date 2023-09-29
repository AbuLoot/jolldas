<ul class="dropdown-menu dropdown-menu-items">
  @foreach($tracks as $track)
    <li><a href="/{{ $lang }}/admin/tracks/{{ $track->id }}/edit"><b>{{ $track->code }}</b></a></li>
  @endforeach
</ul>