@extends('admin-layout.app')

@section('content')
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Playlist de {{ $userSelected->first_name }} {{ $userSelected->last_name }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item"><a href="{{ route('users') }}">Usuarios</a></li>
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
                    
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col text-right">
                                <button type="button" class="btn btn-success" data-toggle="modal"
                                    data-target="#newPlaylistModal">{{ __('New Playlist') }}</button>
                                @if (count($list) > 0)
                                <button type="button" class="btn btn-outline-danger" data-toggle="modal"
                                    data-target="#delPlaylistModal">{{ __('Remove Playlist') }}</button>
                                @endif
                            </div>
                        </div>
                        <div class="row text-center mt-4">
                            <div class="col">
                                <div class="row my-2">
                                    <div class="col-md-3 col-sm-12">
                                        <h2>{{ __('Edit Playlist') }}</h2>
                                        <dl class="py-2">
                                            @foreach ($list as $playlist)
                                            <dt class="border border-secondary py-4 px-2 my-2 text-center">
                                                <h4><a href="{{ route('playlist_details_admin', ['id' => $playlist->id, 'uuid' => $userSelected->id]) }}">{{ $playlist->name }}</a></h4>
                                                {{--<span>{{ __('extra description here') }}</span>--}}
                                            </dt>
                                            @endforeach
                                        </dl>
                                    </div>
                                    <div class="col-md-9 col-sm-12">
                                        <h2>{{ __('Edit Screen Playlists') }}</h2>
                                        <div class="row">
                                            @foreach ($screens as $screen)
                                            <div class="col-md-4 py-3">
                                                <a class="btn btn-success btn-lg btn-block" href="{{ route('screen_content', ['uuid' => $screen->uuid]) }}" role="button">
                                                    {{ $loop->iteration }}. {{ $screen->name }} <br />
                                                    <small>UID: {{ $screen->uuid }}</small>
                                                </a>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
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
  <div class="modal fade" id="newPlaylistModal" tabindex="-1" role="dialog" aria-labelledby="newPlaylistModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form action="{{ route('playlist') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="newPlaylistModalLabel">{{ __('New Playlist') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                <input type="hidden" name="idUser" id="idUser" value="{{ $userSelected->id }}" />
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" id="name" required />
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="interval">{{ __('Content Interval') }} <small>({{ __('only images') }})</small></label>
                            <input type="number" class="form-control" name="interval" id="interval" min="5" max="300" value="10" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-success" id="submit">{{ __('Create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="delPlaylistModal" tabindex="-1" role="dialog" aria-labelledby="delPlaylistModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="addToPlaylistForm" action="{{ route('playlist') }}" method="post">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title" id="delPlaylistModalLabel">{{ __('Remove Playlist') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <input type="hidden" name="idUser" id="idUser" value="{{ $userSelected->id }}" />
                    <div class="form-group">
                        <label for="contentPlayList">{{ __('Select Playlist') }}</label>
                        <select class="form-control" id="contentPlayList" name="id">
                        @forelse ($list as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @empty
                        <option disabled>{{ __('You have no playlists yet') }}...</option>
                        @endforelse
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger" id="submit">{{ __('Remove') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection