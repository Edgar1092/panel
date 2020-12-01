<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    {{-- <meta name="viewport" content="width=device-width, initial-scale=1"> --}}
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AdHook') }}</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Magra:wght@400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/icofont.min.css') }}" rel="stylesheet">
    @yield('styles')
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{-- config('app.name', 'Laravel') --}}
                    {{--<div class="title-logo mx-4">
                        Ad<span>Hook</span>
                    </div>--}}
                    <img width="110" height="32"
                        src="{{ asset('images/logo.png') }}"
                        data-src="{{ asset('images/logo.png') }}"
                        class="header_logo header-logo lazy-loaded ls-is-cached lazyloaded"
                        alt="AdHook.es" />
                </a>
                <button class="navbar-dark navbar-toggler text-white" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                    </ul>

                    <ul class="navbar-nav ml-auto">
                        @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                        @else
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('profile') }}" role="button" aria-expanded="false" v-pre>
                                <i class="fas fa-tools fa-lg"></i>
                            </a>
                        </li>
                        {{--<li class="nav-item">
                            <a class="nav-link" href="#" role="button" aria-expanded="false" v-pre>
                                <i class="fas fa-chart-pie fa-lg"></i>
                            </a>
                        </li>--}}
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle text-white" href="#" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{-- {{ Auth::user()->name }} <span class="caret"></span> --}}
                                <i class="fas fa-bars fa-lg"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @yield('extranav')

        <div class="container-fluid">
            <div class="row">
                @auth
                <nav class="col-md-1 d-none d-md-block bg-light sidebar border-right pt-2">
                    <div class="sidebar-sticky">
                        <ul class="nav flex-column text-center">
                            <!-- <li class="nav-item my-2">
                                <a class="nav-link text-info px-0" href="{{ route('profile') }}">
                                    <i class="fas fa-user-alt fa-2x"></i>
                                </a>
                            </li> -->
                            <li class="nav-item mt-2 mb-4">
                                <a href="{{ route('profile') }}">
                                    <img src="{{ asset('storage/' .  Auth::user()->id . '/avatars/' . Auth::user()->avatar) }}" class="rounded-circle"><br/>
                                    <small><b>{{ Auth::user()->name }}</b></small>
                                </a>
                            </li>
                            <li class="nav-item my-2">
                                <a class="nav-link nav-icon px-0" data-toggle="tooltip" data-placement="right" title="{{ __('Screens') }}" href="{{ route('screens') }}">
                                    <i class="fas fa-tv fa-2x"></i>
                                </a>
                            </li>
                            <li class="nav-item my-2">
                                <a class="nav-link nav-icon px-0" data-toggle="tooltip" data-placement="right" title="{{ __('Multimedia') }}" href="{{ route('content') }}">
                                    <i class="fas fa-photo-video fa-2x"></i>
                                </a>
                            </li>
                            <li class="nav-item my-2">
                                <a class="nav-link nav-icon px-0" data-toggle="tooltip" data-placement="right" title="{{ __('Playlist') }}" href="{{ route('playlist') }}">
                                    <i class="icofont-ui-video-play fa-2x"></i>
                                </a>
                            </li>
                            <li class="nav-item my-2">
                                <a class="nav-link nav-icon px-0" data-toggle="tooltip" data-placement="right" title="{{ __('Apps') }}" href="{{ route('design') }}">
                                    <i class="fas fa-puzzle-piece fa-2x"></i>
                                </a>
                            </li>
                            <li class="nav-item my-2">
                                <a class="nav-link nav-icon px-0" data-toggle="tooltip" data-placement="right" title="{{ __('Editor') }}" href="{{ route('apps') }}">
                                    <i class="fas fa-edit fa-2x"></i>
                                </a>
                            </li>
                            @if (Auth::user()->is_admin)
                            <li class="nav-item my-2">
                                <a class="nav-link nav-icon px-0" data-toggle="tooltip" data-placement="right" title="{{ __('Users') }}" href="{{ route('users') }}">
                                    <i class="fas fa-user fa-2x"></i>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </nav>
                @endauth
                @guest
                <main role="main" class="col-12 py-4">
                @else
                <main role="main" class="col-md-10 ml-sm-auto col-lg-11 py-4">
                @endguest
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>

</html>