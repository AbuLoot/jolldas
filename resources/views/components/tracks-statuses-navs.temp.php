  <!-- Reception -->
    <ul class="nav nav-tabs mb-3 d-none">
      @canany(['reception', 'sending'], Auth::user())
        <li class="nav-item">
          <a class="nav-link bg-light active" aria-current="page">Reception</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/sending">Send</a>
        </li>
      @endcanany
      @can('sorting', Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/sorting"><i class="bi bi-dpad"></i> Sorting</a>
        </li>
      @endcan
      @canany(['arrival', 'giving'], Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/arrival">Arrival</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/giving">Giving</a>
        </li>
      @endcanany
    </ul>

<!-- Sending -->

    <ul class="nav nav-tabs mb-3 d-none">
      @canany(['reception', 'sending'], Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage">Reception</a>
        </li>
        <li class="nav-item dropdown">
          <?php $icons = ['list' => 'card-checklist', 'group' => 'collection']; ?>
          <a class="nav-link bg-light active dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
            <i class="bi bi-{{ $icons[$mode] }}"></i> Send
          </a>
          <ul class="dropdown-menu">
            <li><a wire:click="setMode('list')" class="dropdown-item" href="#"><i class="bi bi-card-checklist"></i> List tracks</a></li>
            <li><a wire:click="setMode('group')" class="dropdown-item" href="#"><i class="bi bi-collection"></i> Group tracks</a></li>
          </ul>
        </li>
      @endcanany
      @can('sorting', Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/sorting"><i class="bi bi-dpad"></i> Sorting</a>
        </li>
      @endcan
      @canany(['arrival', 'giving'], Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/arrival">Arrival</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/giving">Giving</a>
        </li>
      @endcanany
    </ul>

<!-- Sorting -->

    <ul class="nav nav-tabs mb-3 d-none">
      @canany(['reception', 'sending'], Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage">Reception</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/sending">Send</a>
        </li>
      @endcanany
      @can('sorting', Auth::user())
        <li class="nav-item dropdown">
          <a class="nav-link bg-light active" area-current="page"><i class="bi bi-dpad"></i> Sorting</a>
        </li>
        <?php $icons = ['list' => 'card-checklist', 'group' => 'collection']; ?>
        <!-- <li class="nav-item dropdown">
          <a class="nav-link bg-light active dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
            <i class="bi bi-{{ $icons[$mode] }}"></i> Sorting
          </a>
          <ul class="dropdown-menu">
            <li><a wire:click="setMode('list')" class="dropdown-item" href="#"><i class="bi bi-card-checklist"></i> List tracks</a></li>
            <li><a wire:click="setMode('group')" class="dropdown-item" href="#"><i class="bi bi-collection"></i> Group tracks</a></li>
          </ul>
        </li> -->
      @endcan
      @canany(['arrival', 'giving'], Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/arrival">Arrival</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/giving">Giving</a>
        </li>
      @endcanany
    </ul>


<!-- Arrival -->

    <ul class="nav nav-tabs mb-3 d-none">
      @canany(['reception', 'sending'], Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage">Reception</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/sending">Send</a>
        </li>
      @endcanany
      @can('sorting', Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/sorting"><i class="bi bi-dpad"></i> Sorting</a>
        </li>
      @endcan
      @canany(['arrival', 'giving'], Auth::user())
        <li class="nav-item dropdown">
          <?php $icons = ['list' => 'card-checklist', 'group' => 'collection']; ?>
          <a class="nav-link bg-light active dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
            <i class="bi bi-{{ $icons[$mode] }}"></i> Arrival
          </a>
          <ul class="dropdown-menu">
            <li><a wire:click="setMode('list')" class="dropdown-item" href="#"><i class="bi bi-card-checklist"></i> List tracks</a></li>
            <li><a wire:click="setMode('group')" class="dropdown-item" href="#"><i class="bi bi-collection"></i> Group tracks</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/giving">Giving</a>
        </li>
      @endcanany
    </ul>

    <!-- Giving -->
    
    
    <ul class="nav nav-tabs mb-3 d-none">
      @canany(['reception', 'sending'], Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage">Reception</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/sending">Send</a>
        </li>
      @endcanany
      @can('sorting', Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/sorting"><i class="bi bi-dpad"></i> Sorting</a>
        </li>
      @endcan
      @canany(['arrival', 'giving'], Auth::user())
        <li class="nav-item">
          <a class="nav-link" href="/{{ $lang }}/storage/arrival">Arrival</a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-light active" area-current="page">Giving</a>
        </li>
      @endcanany
    </ul>
