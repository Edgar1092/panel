@extends('layouts.app')

@section('styles')
<link href="{{ asset('/css/dropzone.min.css') }}" rel="stylesheet">
<style>
    .card-body {
        padding: 0;
        margin: 0;
    }
</style>
@endsection

@section('content')
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
@php 
$scheduleLock =false;
@endphp
<div class="container-fluid">
    <form action="{{ route('set_playlist_screen', ['uuid' => $screen->uuid]) }}" method="post">
        @csrf
        <input type="hidden" name="screen" value="$screen->uuid">
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
                <button disabled="{{$schedulesUserBlock > 0 ? true : false}}" type="submit" class="btn btn-outline-danger btn-sm mt-2" name="action" value="clear" >{{ __('Clear Schedule') }}</button>
            </div>
            <div class="col-md-9">
                <div class="row text-center">
                    <div class="col">
                        <h4>{{ __('Time') }}</h4>
                    </div>
                    <div class="col">
                        <h4>{{ __('Playlist') }}</h4>
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
                            @php 
                            $scheduleLock =false;
                            @endphp
                                <div class="row text-center">
                                    <div class="col my-2">
                                        {{ $schedule->init_at }} - {{ $schedule->ends_at }}
                                    </div>
                                    <div class="col my-2">
                                        <select class="form-control" name="playlist[{{$schedule->id}}]"  @foreach ($schedulesUsuario as $value)
                                                                        @if($value->schedule_id == $schedule->id && $value->locked == 1)
                                                                            disabled
                                                                         @php
                                                                          $scheduleLock = true;
                                                                         @endphp
                                                                        @endif
                                                                    @endforeach>
                                            @if($scheduleLock == true)
                                            <option value="">Hora bloqueada</option>
                                            @else
                                            <option value="none">{{ __('None') }}</option>
                                            @forelse ($playlists as $item)
                                            <option value="{{ $item->id }}"
                                                @foreach ($actives as $value)
                                                    @if($value->schedule_id == $schedule->id && $value->playlist_id == $item->id && $value->fulltime == 0)
                                                        selected
                                                    @endif
                                                    @if($value->schedule_id == $schedule->id && $value->playlist_id == $item->id && $value->locked==1)
                                                        disabled
                                                    @endif
                                                @endforeach
                                            >{{ $item->name }}</option>
                                            @empty
                                            <option disabled>{{ __('You have no playlists yet') }}...</option>
                                            @endforelse
                                            @endif
                                        </select>
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
                            @php 
                            $scheduleLock =false;
                            @endphp
                                <div class="row text-center">
                                    <div class="col my-2">
                                        {{ $schedule->init_at }} - {{ $schedule->ends_at }}
                                    </div>
                                    <div class="col my-2">
                                        <select class="form-control" name="playlist[{{$schedule->id}}]" @foreach ($schedulesUsuario as $value)
                                                                        @if($value->schedule_id == $schedule->id && $value->locked == 1)
                                                                            disabled
                                                                            @php
                                                                          $scheduleLock = true;
                                                                         @endphp
                                                                        @endif
                                                                    @endforeach>
                                            @if($scheduleLock == true)
                                            <option value="">Hora bloqueada</option>
                                            @else
                                            <option value="none">{{ __('None') }}</option>
                                            @forelse ($playlists as $item)
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
                                            @endif
                                        </select>
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
                            @php 
                            $scheduleLock =false;
                            @endphp
                                <div class="row text-center">
                                    <div class="col my-2">
                                        {{ $schedule->init_at }} - {{ $schedule->ends_at }}
                                    </div>
                                    <div class="col my-2">
                                        <select class="form-control" name="playlist[{{$schedule->id}}]"
                                        @foreach ($schedulesUsuario as $value)
                                                                        @if($value->schedule_id == $schedule->id && $value->locked == 1)
                                                                            disabled
                                                                            @php
                                                                          $scheduleLock = true;
                                                                         @endphp
                                                                        @endif
                                                                    @endforeach>
                                            @if($scheduleLock == true)
                                            <option value="">Hora bloqueada</option>
                                            @else
                                            <option value="none">{{ __('None') }}</option>
                                            @forelse ($playlists as $item)
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
                                            @endif
                                        </select>
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
                                @forelse ($playlists as $item)
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


@endsection