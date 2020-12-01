@extends('layouts.app')

@section('styles')
<link href="{{ asset('/css/dropzone.min.css') }}" rel="stylesheet">
<style>
    .card-body {
        padding: 0;
        margin: 0;
    }
    .card-body img {
        width: 100%;
        height: 100%;
    }
    .card {
        height: 10rem;
    }
    .dropzone.dz-started .dz-message {
        display: inherit;
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

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <p class="font-weight-bolder text-break">{{ $playlist->name }}</b></p>
            {{ __('Created At') }}: {{ $playlist->created_at }}<br />
            {{ __('Images') }}: {{ $count['images'] }}<br />
            {{ __('Videos') }}: {{ $count['videos'] }}<br />
            {{ __('Interval') }}: {{ $playlist->interval }}</p>
            <button type="button" class="btn btn-outline-primary btn-sm mb-1" data-toggle="modal"
                data-target="#updatePlaylistModal">{{ __('Edit Playlist') }}</button><br/>
            <a href="{{ route('content') }}" class="btn btn-outline-secondary btn-sm my-1"
                role="button">{{ __('Go to my content') }}</a>
        </div>
        <div class="col-md-9">
            <div class="row mb-4">
                <div class="col text-left">
                    <button type="button" class="btn btn-outline-success btn-sm" data-toggle="modal"
                        data-target="#addContentModal">{{ __('Add Content') }}</button>
                </div>
                <div class="col text-right">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal"
                        data-target="#removeContentModal">{{ __('Remove Selection') }}</button>
                </div>
            </div>
            <div class="row">
                @forelse ($content as $item)
                <div class="col-md-3 col-lg-2 mb-4">
                    <div class="card border" data-id="{{ $item->id }}">
                        <div class="card-header text-truncate">
                            {{ $item->name }}
                        </div>
                        <div class="card-body">
                        @switch($item->type)
                            @case('video')
                                <img src="{{ getVideoPreview($user->id . '/content/' . $item->name) }}" alt="{{ $item->name }}">
                                @break

                            @default
                                <img src="{{ asset('storage/' .  $user->id . '/content/' . $item->name) }}" alt="{{ $item->name }}">
                        @endswitch
                        </div>
                    </div>
                </div>
                @empty
                <div class="col text-center">
                    <h5>{{ __('You have no content yet') }}...</h5>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addContentModal" tabindex="-1" role="dialog" aria-labelledby="addContentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form action="{{ route('content') }}" method="post"
                enctype="multipart/form-data" id="my-dropzone" class="dropzone">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addContentModalLabel">{{ __('Upload Content') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        {{-- <div class="card-header">
                            {{ $playlist->name }}
                        </div> --}}
                        <div class="card-body">
                            <div class="dz-message text-success">
                                {{ __('Drop your files here (max. 10 files)') }}<br/>
                                {{ __('Click to search') }}
                            </div>
                            <div class="dropzone-previews"></div>
                            <input type="hidden" name="playlist" value="{{ $playlist->id }}">
                        </div>
                        <div class="card-footer text-center">
                            <small><b>{{ __('Please, do not close or cancel and wait until conversion finish') }}</b></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-success" id="submit">{{ __('Upload') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="removeContentModal" tabindex="-1" role="dialog" aria-labelledby="removeContentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form action="{{ route('remove_playlist_details', ['id' => $playlist->id]) }}" method="post"
                enctype="multipart/form-data" id="removeContentForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="removeContentModalLabel">{{ __('Remove Content') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <h4>{{ __('You are about to remove selected content from the current list, are you agree?') }}</h4>
                    <input type="hidden" name="playlist" value="{{ $playlist->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger" id="submit">{{ __('Remove') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updatePlaylistModal" tabindex="-1" role="dialog" aria-labelledby="updatePlaylistModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form action="{{ route('playlist_details', ['id' => $playlist->id]) }}" method="post">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePlaylistModalLabel">{{ __('Update Playlist') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ $playlist->name }}" required />
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
                    <button type="submit" class="btn btn-success" id="submit">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/dropzone.min.js') }}"></script>
<script>
    let updatePage = false;

    Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
    Dropzone.options.myDropzone = {
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 1,
        addRemoveLinks: true,
        withCredentials: true,
        
        init: function() {
            var submitBtn = document.querySelector("#submit");
            myDropzone = this;
            
            submitBtn.addEventListener("click", function(e){
                e.preventDefault();
                e.stopPropagation();
                myDropzone.processQueue();
            });
            this.on("addedfile", function(file) {
                //alert("file uploaded");
            });
            
            this.on("complete", function(file) {
                myDropzone.removeFile(file);
                updatePage = true;
            });

            this.on("success", 
                myDropzone.processQueue.bind(myDropzone)
            );
        },
        
        uploadprogress: function(file, progress, bytesSent) {
            if (file.previewElement) {
                if (progress > 99) {
                    file.previewElement.querySelector(".dz-size").textContent = "Convirtiendo";
                }
            }
        }
    };

    $("div.card.border").click(function(e){
        $(this).toggleClass('border-success border-2');
    });

    $('#addContentModal').on('hidden.bs.modal', function () {
        if (updatePage) location.reload();
    });

    $("#removeContentForm").submit(function(e){
        //e.preventDefault();

        let submitForm = false;
        $('#removeContentForm').find('input[name="content"]').remove();

        $('div.card.border.border-success.border-2').each(function(){
            submitForm = true;
            const value = $(this).data('id');
            $('<input />').attr('type', 'hidden')
                .attr('name', 'content[]')
                .attr('value', value)
                .appendTo('#removeContentForm');
        });

        if (submitForm) {
            return true;
        } else {
            alert("{{ __('You have no selection') }}");
            return false;
        }
    });
</script>
@endsection
