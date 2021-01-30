@extends('admin-layout.app')
@section('styles')
<link href='https://api.mapbox.com/mapbox-gl-js/v1.8.1/mapbox-gl.css' rel='stylesheet' />
<style>{{--#addScreenModalLocation, #viewScreenModalLocation { width: 100%; }
    #addScreenModalLocation { height: 300px; }--}}
    #viewScreenModalLocation { height: width: 100%; 100%; }
    .select-screen { background-color: #e3f2fd; cursor: pointer; }
    .select-screen-off { background-color: #fdd3d3; cursor: pointer; }
    .text-on { color: #e3f2fd; }
    .text-off { color: #fdd3d3; }
</style>
@endsection
@section('content')
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Pantallas de {{ $userSelected->first_name }} {{ $userSelected->last_name }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item"><a href="{{ route('users') }}">Usuarios</a></li>
              <li class="breadcrumb-item active">Pantallas</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      <div class="row">
          <div class="col-md-12">
              <div class="card">
                  <div class="card-header">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-{{ Session::get('color') ?? 'success' }} alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>@lang($message)</strong>
                    </div>
                    @endif

                    @if ($message = Session::get('error'))
                    <div class="alert alert-{{ Session::get('color') ?? 'danger' }} alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>@lang($message)</strong>
                    </div>
                    @endif

                    @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>{{ __('Whoops') }}!</strong> {{ __('There were some problems adding new screen') }}.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>@lang($error)</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                  </div>
                  <div class="card-body">
                  <div class="row">
                    <div class="col-md-3">
                        <h3>{{ __('Screens') }} <small style="font-size:50%;"><i>{{ count($screens) }} {{ __('out of') }} {{ count($screens) }}</i></small>
                        </h3>
                        <label> <i class="fas fa-circle text-success"></i> {{$screensOn}} Online</label><br>
                        <label> <i class="fas fa-circle text-danger"></i> {{$screensOff}} Offline</label><br>
                        <h3>Propias</h3>

                        @foreach ($screens as $screen)
                        @if($screen->offline === 0)
                        <div class="row my-2 select-screen"
                            data-uuid="{{ $screen->uuid }}"
                            data-name="{{ $screen->name }}"
                            data-lat="{{ $screen->lat }}"
                            data-lng="{{ $screen->lng }}"
                            data-promotor="0"
                            data-href="{{ route('screen_content', ['uuid' => $screen->uuid]) }}">
                            <div class="col-md-12 py-3">
                                <button type="button" class="close text-danger"
                                    data-uuid="{{ $screen->uuid }}"
                                    data-name="{{ $screen->name }}"
                                    data-iduser="{{ $screen->user->id }}"
                                    data-toggle="modal"
                                    data-target="#delScreenModal">×</button>
                                {{ $loop->iteration }}. {{ $screen->name }} <br />
                                <small>UID: {{ $screen->uuid }}</small>
                            </div>
                        </div>
                        @else
                        <div class="row my-2 select-screen select-screen-off"
                            data-uuid="{{ $screen->uuid }}"
                            data-name="{{ $screen->name }}"
                            data-lat="{{ $screen->lat }}"
                            data-lng="{{ $screen->lng }}"
                            data-promotor="0"
                            data-href="{{ route('screen_content', ['uuid' => $screen->uuid]) }}">
                            <div class="col-md-12 py-3">
                                <button type="button" class="close text-danger"
                                    data-uuid="{{ $screen->uuid }}"
                                    data-name="{{ $screen->name }}"
                                    data-iduser="{{ $screen->user->id }}"
                                    data-toggle="modal"
                                    data-target="#delScreenModal">×</button>
                                {{ $loop->iteration }}. {{ $screen->name }} <br />
                                <small>UID: {{ $screen->uuid }}</small>
                            </div>
                        </div>
                        @endif
                        @endforeach
                        @if($userSelected->is_promotor)
                        <h3>Asignadas</h3>
                        @foreach ($screensPromotor as $screen)
                        @if($screen->offline === 0)
                        <div class="row my-2 select-screen"
                            data-uuid="{{ $screen->uuid }}"
                            data-name="{{ $screen->name }}"
                            data-lat="{{ $screen->lat }}"
                            data-lng="{{ $screen->lng }}"
                            data-promotor="1"
                            data-href="{{ route('screen_content', ['uuid' => $screen->uuid]) }}">
                            <div class="col-md-12 py-3">
                                {{ $loop->iteration }}. {{ $screen->name }} <br />
                                <small>UID: {{ $screen->uuid }}</small>
                            </div>
                        </div>
                        @else
                        <div class="row my-2 select-screen select-screen-off"
                            data-uuid="{{ $screen->uuid }}"
                            data-name="{{ $screen->name }}"
                            data-lat="{{ $screen->lat }}"
                            data-lng="{{ $screen->lng }}"
                            data-promotor="1"
                            data-href="{{ route('screen_content', ['uuid' => $screen->uuid]) }}">
                            <div class="col-md-12 py-3">
                                {{ $loop->iteration }}. {{ $screen->name }} <br />
                                <small>UID: {{ $screen->uuid }}</small>
                            </div>
                        </div>
                        @endif
                        @endforeach
                        @endif
                    </div>
                    <div class="col-md-9 pl-4">
                        <div class="row">
                            <div class="col text-right">
                                <a class="btn btn-link text-secondary d-none" href="#!" id="viewSreenContent">
                                    <!-- <i class="fas fa-border-all fa-2x"></i> -->
                                    <i class="fas fa-cogs fa-2x"></i>
                                </a> 
                                <a class="btn btn-link text-secondary d-none" href="#!" data-toggle="modal"
                                data-target="#viewScreenModal">
                                    <i class="fas fa-map-marker-alt fa-2x"></i>
                                </a>
                                {{--<a class="btn btn-outline-secondary" href="#!" role="button">{{ __('Add Group') }}</a>--}}
                                <a class="btn btn-outline-secondary" href="#!" role="button" data-toggle="modal"
                                    data-target="#screenModal">{{ __('Add Screen') }}</a> 
                                @if($userSelected->is_promotor)
                                <a class="btn btn-outline-secondary" href="#!" role="button" data-toggle="modal"
                                    data-target="#assignScreenModal">Asignar pantalla</a> 
                                @endif
                            </div>
                        </div>
                        @foreach ($screens as $screen)
                        <form class="row screen-details d-none" id="s-{{ $screen->uuid }}"
                            action="{{ route('screens', ['uuid' => $screen->uuid]) }}" method="post">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="uuid" value="{{ $screen->uuid }}" />
                            <input type="hidden" name="idUser" value="{{ $screen->user->id }}" />
                            <div class="col text-left">
                                <p class="font-weight-bolder text-break">{{ $screen->name }}<br/>
                                    <button type="button" class="btn btn-outline-secondary btn-sm my-2"
                                        data-toggle="modal" data-target="#updateScreenModal{{$screen->id}}">
                                        {{ __('Change Name') }}
                                    </button>
                                </p>
                                <p>{{ __('OS') }}: {{ $screen->os }}<br />
                                {{ __('Version') }}: {{ $screen->version }}<br />
                                {{ __('Brand') }} / {{ __('Model') }}: {{ $screen->brand }}<br />
                                {{ __('Manufacturer') }}: {{ $screen->manufacturer }}<br />
                                {{ __('Add Date') }}: {{ $screen->created_at }}</p>
                                <p>
                                {{ __('Esta reproduciendo') }}: {{ $screen->reproduce ? 'Si' : 'No' }}<br>
                                @if($screen->reproduce)
                                {{ __('Playlist que se reproduce') }}: {{ $screen->playlist_reproduce->name }}
                                @endif
                                </p>
                                @if($user->is_admin)
                                <p>
                                {{ __('Name') }}: {{ $screen->user->first_name }} {{ $screen->user->last_name }}<br />
                                {{ __('E-Mail Address') }}: {{ $screen->user->email }}<br />
                                {{ __('Phone') }}: {{ $screen->user->phone }}<br />
                                {{ __('User type') }}: {{ $screen->user->is_admin ? "Administrador" : "Cliente" }}<br />
                                </p>
                                @endif
                                <p>{{ __('Content Type') }}:
                                @if($screen->offline === 0)
                                <b><span class="text-success">{{ __('Online') }}</span></b></p>
                                <p><button type="submit" class="btn btn-outline-danger btn-sm">{{ __('Change Online') }}</button></p>
                                <input type="hidden" name="offline" value="1" />
                                @else
                                <b><span class="text-danger">{{ __('Offline') }}</span></b></p>
                                <p><button type="submit" class="btn btn-outline-success btn-sm">{{ __('Change Offline') }}</button></p>
                                <p><button type="submit" name="force" value="1" class="btn btn-outline-info btn-sm">{{ __('Force Download') }}</button></p>
                                <input type="hidden" name="offline" value="0" />
                                @endif
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('screen_content', ['uuid' => $screen->uuid]) }}" role="button">{{ __('Edit Playlist') }}</a>
                            </div>
                        </form>
                        @endforeach
                        @if($userSelected->is_promotor)
                        @foreach ($screensPromotor as $screen)
                        <form class="row screen-details d-none" id="s-{{ $screen->uuid }}"
                            action="{{ route('screens', ['uuid' => $screen->uuid]) }}" method="post">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="uuid" value="{{ $screen->uuid }}" />
                            <div class="col text-left">
                                <p class="font-weight-bolder text-break">{{ $screen->name }}<br/>
                                </p>
                                <p>{{ __('OS') }}: {{ $screen->os }}<br />
                                {{ __('Version') }}: {{ $screen->version }}<br />
                                {{ __('Brand') }} / {{ __('Model') }}: {{ $screen->brand }}<br />
                                {{ __('Manufacturer') }}: {{ $screen->manufacturer }}<br />
                                {{ __('Add Date') }}: {{ $screen->created_at }}</p>
                                <p>
                                {{ __('Esta reproduciendo') }}: {{ $screen->reproduce ? 'Si' : 'No' }}<br>
                                @if($screen->reproduce)
                                {{ __('Playlist que se reproduce') }}: {{ $screen->playlist_reproduce->name }}
                                @endif
                                </p>
                                <p>{{ __('Content Type') }}:
                                @if($screen->offline === 0)
                                <b><span class="text-success">{{ __('Online') }}</span></b></p>
                                <!-- <p><button type="submit" class="btn btn-outline-danger btn-sm">{{ __('Change Online') }}</button></p>
                                <input type="hidden" name="offline" value="1" /> -->
                                @else
                                <b><span class="text-danger">{{ __('Offline') }}</span></b></p>
                                <!-- <p><button type="submit" class="btn btn-outline-success btn-sm">{{ __('Change Offline') }}</button></p>
                                <p><button type="submit" name="force" value="1" class="btn btn-outline-info btn-sm">{{ __('Force Download') }}</button></p>
                                <input type="hidden" name="offline" value="0" /> -->
                                @endif
                                <!-- <a class="btn btn-outline-secondary btn-sm" href="{{ route('screen_content', ['uuid' => $screen->uuid]) }}" role="button">{{ __('Edit Playlist') }}</a> -->
                            </div>
                        </form>
                        @endforeach
                        @endif
                    </div>
                </div>
                  </div>
              </div>
          </div>
      </div>

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@foreach ($screens as $screen)
<div class="modal fade" id="updateScreenModal{{$screen->id}}" tabindex="-1" role="dialog" aria-labelledby="updateScreenModal{{$screen->id}}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form action="{{ route('screens', ['uuid' => $screen->uuid]) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="uuid" value="{{ $screen->uuid }}" />
                <div class="modal-header">
                    <h5 class="modal-title" id="updateScreenModal{{$screen->id}}Label">{{ __('Update Screen') }}: {{ $screen->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="hidden" class="form-control" name="idUser" id="idUser" value="{{ $screen->user->id }}" />
                            <input type="text" class="form-control" name="name" id="name" value="{{ $screen->name }}" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success" name="action" value="info">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<div class="modal fade" id="screenModal" tabindex="-1" role="dialog" aria-labelledby="screenModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('screens') }}" method="POST" id="addScreenModal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="screenModalLabel">{{ __('Add Screen') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($user->is_admin)
                    
                    <div class="form-group">
                        <label for="users">{{ __('Users') }}</label>
                        <select class="form-control" name="userSelected" id="userSelected" >
                            <option selected value="">Seleccione...</option>
                            @foreach ($users as $user)
                            <option {{ $user->id == $userSelected->id ? 'selected' : '' }} value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="uuid">{{ __('UUID') }}</label>
                        <input type="text" class="form-control" name="uuid" id="uuid" required />
                    </div>
                    <div class="form-group">
                        <label for="name">{{ __('Name') }}</label>
                        <input type="text" class="form-control" name="name" id="name" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('Add Screen') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewScreenModal" tabindex="-1" role="dialog" aria-labelledby="viewScreenModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewScreenModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewScreenModalLocation"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delScreenModal" tabindex="-1" role="dialog" aria-labelledby="delScreenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form action="{{ route('screens') }}" method="POST">
                @csrf
                @method("delete")
                <div class="modal-header">
                    <h5 class="modal-title" id="delScreenModalLabel">{{ __('Remove Screen') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    {{-- You're about to remove <b><span class="screen-name"></span></b> screen, want to proceed? --}}
                    <h4>{{ __('You are about to remove this screen') }}</h4>
                    <h5 class="screen-name my-2"></h5>
                    <h4 class="my-3">{{ __('Want to proceed?') }}</h4>
                    <input type="hidden" name="uuid" id="delScreenUuid">
                    <input type="hidden" class="form-control" name="idUser" id="delScreenidUser" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Remove Screen') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="assignScreenModal" tabindex="-1" role="dialog" aria-labelledby="screenModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('screens_assign') }}" method="POST" id="assignScreenForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="screenModalLabel">Asignar pantalla</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="uuid">{{ __('UUID') }}</label>
                        <input type="hidden" name="userSelected" id="userSelected" value="{{ $userSelected->id }}">
                        <input type="text" class="form-control" name="uuid" id="uuid" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-success">Asignar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('js/app.js') }}"></script>
<script>
    let selectedScreen = {};
    const contentUrl = "{{ substr(route('screen_content', ['uuid' => 0]), 0, -1) }}";

    const mapViewModal = new mapboxgl.Map({
        container: 'viewScreenModalLocation',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [40.463667, -3.74922],
        zoom: 2
    });

    const markerView = new mapboxgl.Marker()
        .setLngLat([0, 0])
        .addTo(mapViewModal);

    $('#delScreenModal').on('show.bs.modal', function (e) {
        $modal = $(this);
        const button = $(e.relatedTarget);

        const name = button.data('name');
        const uuid = button.data('uuid');
        const iduser = button.data('iduser');
        
        $modal.find('.screen-name').text(name);
        $modal.find('#delScreenUuid').val(uuid);
        $modal.find('#delScreenidUser').val(iduser);
    });

    $('#viewScreenModal').on('show.bs.modal', function (e) {
        $('#viewScreenModalLabel').text(selectedScreen.name);
    });

    $('.select-screen').click(function (e) {
        e.preventDefault();
        $screen = $(this);

        $('.select-screen').removeClass('bg-success text-white');
        $screen.addClass('bg-success text-white');

        $('.screen-details').addClass('d-none');
        promotor = $screen.data('promotor')
        selectedScreen = {
            uuid: $screen.data('uuid'),
            name: $screen.data('name'),
            lat: $screen.data('lng'),
            lng: $screen.data('lat')
        };



        markerView.setLngLat([
            selectedScreen.lng,
            selectedScreen.lat
        ]);

          console.log('aqui estoy',promotor);

        mapViewModal.flyTo({
            essential: true,
            center: [
                selectedScreen.lng,
                selectedScreen.lat
            ]
        });
        
        $('.text-secondary.d-none').removeClass('d-none');
        $(this).find('#viewScreenModalLabel').text(selectedScreen.name);

        $('#viewSreenContent').attr('href', contentUrl + selectedScreen.uuid);
        $('#s-' + selectedScreen.uuid).removeClass('d-none');
        if(promotor == 1){
            $('#viewSreenContent').addClass('d-none');
        }
    }).dblclick(function (e) {
        e.preventDefault();
        window.location.href = $(this).data("href");
    });
</script>
@endsection