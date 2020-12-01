@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row text-center">
        <div class="col">
            <h2>{{ __('Select screen') }}</h2>
            <div class="row my-2">
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
@endsection