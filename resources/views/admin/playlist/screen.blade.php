@extends('admin-layout.app')
@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.16/css/bootstrap-multiselect.css" type="text/css">


@endsection
@section('content')
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0">Usuario {{ $userSelected->first_name }} {{ $userSelected->last_name }}</h1>
            <h1 class="m-0">Pantalla {{ $screen->name }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item"><a href="{{ route('users') }}">Usuarios</a></li>
              <li class="breadcrumb-item"><a href="{{ route('screens_admin', ['uuid' => $userSelected->id]) }}">Pantallas</a></li>
              <li class="breadcrumb-item active">Playlist </li>
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
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>@lang($message)</strong>
                            </div>
                            @endif

                            @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> {{ __('There were some problems with your input') }}.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                        <div class="card-body">
                        <div class="col-md-12">
                                        <form method="POST" action="{{ route('block_shcedule') }}">
                                        @csrf
                                        <input type="hidden" name="idUser" value="{{ $userSelected->id }}" />
                                        <div class="form-group">
                                        <!-- {{$schedulesUsuario}} -->
                                        <select id="multi-select-demo" class="form-control" name="schedule_id[]" multiple="multiple">
                                        @foreach ($schedulesFull as $schedule)

                                            <option value="{{ $schedule->id }}"  @foreach ($schedulesUsuario as $value)
                                                                        @if($value->schedule_id == $schedule->id && $value->locked == 1)
                                                                            selected
                                                                         
                                                                        @endif
                                                                    @endforeach>{{ $schedule->init_at }}-{{ $schedule->ends_at }}</option>

                                    
                                            @endforeach
                                        </select>
                                        
                                        <input type="submit" class="btn btn-outline-success" >
                                        </div>
                              
                                        </form>
                                    </div>
                        <form action="{{ route('set_playlist_screen', ['uuid' => $screen->uuid]) }}" method="post">
                            @csrf
                            <input type="hidden" name="screen" value="{{$screen->uuid}}">
                            <input type="hidden" name="fulltimePlaylist" value="none">
                            <div class="row">
                                <div class="col-md-3">
                                    <p class="font-weight-bolder text-break">{{ $screen->name }}</p>
                                    <p class="mb-4">{{ __('OS') }}: {{ $screen->os }}<br />
                                    {{ __('Version') }}: {{ $screen->version }}<br />
                                    {{ __('Add Date') }}: {{ $screen->created_at }}<br />
                                    {{ __('Images') }}: {{ $count['images'] }}<br />
                                    {{ __('Videos') }}: {{ $count['videos'] }}</p>
                                    <button type="button" class="btn btn-success btn-sm mb-4" data-toggle="modal" data-target="#selectPlaylistModal">{{ __('Full-Time') }}</button>
                                    <br/>
                                    <button type="submit" class="btn btn-outline-success btn-sm" name="action" value="update" >{{ __('Update Schedule') }}</button>
                                    <br/>
                                    <button type="submit" class="btn btn-outline-danger btn-sm mt-2" name="action" value="clear" >{{ __('Clear Schedule') }}</button>
                                </div>
                                <div class="col-md-9">
                                    <div class="row text-center">
                                        <div class="col">
                                            <h4>{{ __('Time') }}</h4>
                                        </div>
                                        <div class="col">
                                            <h4>{{ __('Playlist') }}</h4>
                                        </div>
                                        <div class="col">
                                            <h4>Editar Contenido</h4>
                                        </div>
                                    </div>
                           
                                    <div id="accordion">
                                        @if (count($schedules) > 0)
                                        
                                        <div class="card">
                                            <div class="card-header text-center" id="headingOne">
                                                <h5 class="mb-0">
                                                    <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    {{ __('Morning') }}
                                                    </button>
                                                </h5>
                                            </div>
                                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                                <div class="card-body">
                                                
                                                @foreach ($sectionSchedules['morning'] as $schedule)
                                                    <div class="row text-center">
                                                        <div class="col my-2">
                                                            {{ $schedule->init_at }} - {{ $schedule->ends_at }}
                                                        </div>
                                                        <div class="col my-2">
                                                            <select class="form-control" name="playlist[{{$schedule->id}}]">
                                                                <option value="none">{{ __('None') }}</option>
                                                                @forelse ($playlitsFinal as $item)
                                                                <option value="{{ $item->id }}"
                                                                    @foreach ($actives as $value)
                                                                        @if($value->schedule_id == $schedule->id && $value->playlist_id == $item->id && $value->fulltime == 0)
                                                                            selected
                                                                         
                                                                        @endif
                                                                    @endforeach
                                                                >{{ $item->name }}</option>
                                                                @empty
                                                                <option disabled>{{ __('You have no playlists yet') }}...</option>
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                        <div class="col my-2">
                                                     
                                                                @forelse ($playlitsFinal as $item)
                                                                
                                                                    @foreach ($actives as $value)
                                                                        @if($value->schedule_id == $schedule->id && $value->playlist_id == $item->id && $value->fulltime == 0)
                                                                        <a class="btn btn-outline-success" href="{{ route('playlist_details_admin', ['id' => $value->playlist_id, 'uuid' => $userSelected->id]) }}">Editar contenido</a>
                                                                        @endif
                                                                        <!-- @if($value->schedule_id == $schedule->id && $value->playlist_id == $item->id && $value->fulltime == 0 && $value->locked==0)
                                                                        {{$value->locked}}
                                                                            <a class="btn btn-outline-danger"  href="{{ route('block_shcedule', ['id' => $schedule->id , 'uuid' => $userSelected->id,'idScreen' => $screen->uuid]) }}">Bloquear</a>
                                                                            @else
                                                                            <a class="btn btn-outline-success" href="{{ route('unblock_shcedule', ['id' => $schedule->id , 'uuid' => $userSelected->id,'idScreen' => $screen->uuid]) }}">Desbloquear</a>
                                                                            @endif -->
                                                                    @endforeach
                                                                
                                                                @empty
                                                               
                                                                @endforelse
   
                                                              
                                                        </div>
                                                    </div>
                                                @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header text-center" id="headingTwo">
                                            <h5 class="mb-0">
                                                <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                    {{ __('Afternoon') }}
                                                </button>
                                            </h5>
                                            </div>
                                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                                <div class="card-body">
                                                @foreach ($sectionSchedules['afternoon'] as $schedule)
                                                    <div class="row text-center">
                                                        <div class="col my-2">
                                                            {{ $schedule->init_at }} - {{ $schedule->ends_at }}
                                                        </div>
                                                        <div class="col my-2">
                                                            <select class="form-control" name="playlist[{{$schedule->id}}]">
                                                                <option value="none">{{ __('None') }}</option>
                                                                @forelse ($playlitsFinal as $item)
                                                                <option value="{{ $item->id }}"
                                                                    @foreach ($actives as $value)
                                                                        @if($value->schedule_id == $schedule->id && $value->playlist_id == $item->id && $value->fulltime == 0)
                                                                            selected
                                                                        @endif
                                                                    @endforeach
                                                                >{{ $item->name }}</option>
                                                                @empty
                                                                <option disabled>{{ __('You have no playlists yet') }}...</option>
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                        <div class="col my-2">
                                                                @forelse ($playlists as $item)
                                                                
                                                                    @foreach ($actives as $value)
                                                                        @if($value->schedule_id == $schedule->id && $value->playlist_id == $item->id && $value->fulltime == 0)
                                                                        <a class="btn btn-outline-success" href="{{ route('playlist_details_admin', ['id' => $value->playlist_id, 'uuid' => $userSelected->id]) }}">Editar contenido</a>
                                                                        @endif
                                                                    @endforeach
                                                                
                                                                @empty
                                                               
                                                                @endforelse
                                                           
                                                        </div>
                                                    </div>
                                                @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header text-center" id="headingThree">
                                            <h5 class="mb-0">
                                                <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                    {{ __('Night') }}
                                                </button>
                                            </h5>
                                            </div>
                                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                                <div class="card-body">
                                                @foreach ($sectionSchedules['night'] as $schedule)
                                                    <div class="row text-center">
                                                        <div class="col my-2">
                                                            {{ $schedule->init_at }} - {{ $schedule->ends_at }}
                                                        </div>
                                                        <div class="col my-2">
                                                            <select class="form-control" name="playlist[{{$schedule->id}}]">
                                                                <option value="none">{{ __('None') }}</option>
                                                                @forelse ($playlitsFinal as $item)
                                                                <option value="{{ $item->id }}"
                                                                    @foreach ($actives as $value)
                                                                        @if($value->schedule_id == $schedule->id && $value->playlist_id == $item->id && $value->fulltime == 0)
                                                                            selected
                                                                        @endif
                                                                    @endforeach
                                                                >{{ $item->name }}</option>
                                                                @empty
                                                                <option disabled>{{ __('You have no playlists yet') }}...</option>
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                        <div class="col my-2">
                                                                @forelse ($playlists as $item)
                                                                
                                                                    @foreach ($actives as $value)
                                                                        @if($value->schedule_id == $schedule->id && $value->playlist_id == $item->id && $value->fulltime == 0)
                                                                        <a class="btn btn-outline-success" href="{{ route('playlist_details_admin', ['id' => $value->playlist_id, 'uuid' => $userSelected->id]) }}">Editar contenido</a>
                                                                        @endif
                                                                    @endforeach
                                                                
                                                                @empty
                                                               
                                                                @endforelse
                                                           
                                                        </div>
                                                    </div>
                                                @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="col text-center">
                                        <h5>{{ __('You have no scheduled available yet') }}...</h5>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <div class="modal fade" id="selectPlaylistModal" tabindex="-1" role="dialog" aria-labelledby="selectPlaylistModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form action="{{ route('set_playlist_screen', ['uuid' => $screen->uuid]) }}" method="post">
                @csrf
                <input type="hidden" name="screen" value="$screen->uuid">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectPlaylistModalLabel">{{ __('Full-Time Playlist') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @if (count($playlists) > 0)
                <div class="modal-body text-center">
                    <p>{{ __('When you choose the full-time option the selected playlist will be used on the whole set of available schedules') }}</p>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 offset-md-3">
                            <select class="form-control" name="fulltimePlaylist" id="fulltimePlaylist">
                                <option value="none" disabled>{{ __('Select Playlist') }}</option>
                                @forelse ($playlitsFinal as $item)
                                {{--<option value="{{ $item->id }}" {{ ($actives[0]->playlist_id ?? false) == $item->id && ($actives[0]->fulltime ?? 0) == 1 ? 'selected' : '' }}>{{ $item->name }}</option>--}}
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @empty
                                <option disabled>{{ __('You have no playlists yet') }}...</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success" id="submit" name="action" value="update">{{ __('Confirm') }}</button>
                </div>
                @else
                <div class="modal-body text-center">
                    <p>{{ __('You have no playlists yet') }}</p>
                <div class="modal-footer">
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $('.collapse').collapse()
</script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
 
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>





 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.16/js/bootstrap-multiselect.js"></script>
 <script type="text/javascript">
 
    $(document).ready(function() {
 
        // $('#multi-select-demo').multiselect();

        $('#multi-select-demo').multiselect({
            
            includeSelectAllOption: true,

            maxHeight: 350,

            dropUp: true

            });
 
 
    });
 
</script>
@endsection