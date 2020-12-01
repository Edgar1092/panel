@extends('layouts.app')

@section('styles')
<style>
    #nav-overlay {
        background-color: rgba(255, 255, 255, 0.72);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        transition: transform .3s,opacity .6s;
        pointer-events: none;
        z-index: 99999;
        height: 106px;
    }
    .icon-angle-right:before {
        content: "\f105";
    }
    .btn-dark {
        font-family: 'Magra', sans-serif;
        background: black;
        border-radius: unset;
        text-transform: uppercase;
        font-size: 1.15rem;
    }
</style>
@endsection

{{--@section('extranav')
    <div id="nav-overlay"></div>
@endsection--}}

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-4">
            {{-- <div class="card"> --}}
                {{-- <div class="card-header">{{ __('Login') }}</div> --}}

                {{-- <div class="card-body"> --}}
                    
                    <h2 class="text-center my-4">INICIAR SESIÓN</h2>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <!-- <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6"> -->
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <!-- <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6"> -->
                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col text-center">
                                <div class="form-check">
                                    <b>{{ __('Are you new on AdHook?') }}</b>
                                    <a class="btn btn-link p-0" href="{{ route('register') }}">
                                        {{ __('Sign-up') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            {{-- <!-- <div class="col-md-6 offset-md-4"> -->
                            <div class="col-md-6 text-left">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div> --}}
                            <div class="col text-center">
                                <div class="form-check">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link p-0 text-secondary" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <!-- <div class="col-md-8 offset-md-4"> -->
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-dark btn-success px-4">
                                    {{ __('Login') }}
                                </button>

                                <!-- @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif -->
                            </div>
                        </div>
                    </form>

                    {{-- <div class="form-group row mb-0">
                        <div class="col-md-12">
                            Dont have an account? <a class="text-success" href="{{ route('register') }}">Sign Up</a>
                            <!-- @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif -->
                        </div>
                    </div> --}}
                {{-- </div> --}}
            {{-- </div> --}}
        </div>
    </div>
</div>
@endsection
