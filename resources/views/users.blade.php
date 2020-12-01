@extends('layouts.app')

@section('styles')
<link href='https://api.mapbox.com/mapbox-gl-js/v1.8.1/mapbox-gl.css' rel='stylesheet' />
<style>{{--#addScreenModalLocation, #viewScreenModalLocation { width: 100%; }
    #addScreenModalLocation { height: 300px; }--}}
    #viewScreenModalLocation { height: width: 100%; 100%; }
    .select-screen { background-color: #e3f2fd; cursor: pointer; }
</style>
@endsection

@section('content')
@if ($message = Session::get('success'))
<div class="alert alert-{{ Session::get('color') ?? 'success' }} alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>@lang($message)</strong>
</div>
@endif

@if (count($errors) > 0)
<div class="alert alert-danger">
    <strong>{{ __('Whoops') }}!</strong> {{ __('There were some problems adding new user') }}.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>@lang($error)</li>
        @endforeach
    </ul>
</div>
@endif

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 text-right">
            <a class="btn btn-outline-secondary" href="#!" role="button" data-toggle="modal" data-target="#userModal">{{ __('Add User') }}</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3>{{ __('Users') }} <small style="font-size:50%;"><i>{{ count($users) }} {{ __('out of') }} {{ $users->total() }}</i></small>
            </h3>
            
            <div class="row">
            <table class="table table-striped">
                <thead>
                    <tr>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Email</th>
                    <th>Telefono</th>
                    <th>Perfil</th>
                    <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                    <td>{{$user->first_name}}</td>
                    <td>{{$user->last_name}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->phone}}</td>
                    <td>{{$user->is_admin ? 'Administrador' : 'Cliente'}}</td>
                    <td>
                    <button type="button" class="btn btn-outline-primary" data-toggle="tooltip" data-placement="bottom" title="Editar"
                        data-uuid="{{ $user->id }}"
                        data-fname="{{ $user->first_name }}"
                        data-lname="{{ $user->last_name }}"
                        data-email="{{ $user->email }}"
                        data-phone="{{ $user->phone }}"
                        data-tuser="{{ $user->is_admin }}"
                        data-toggle="modal"
                        data-target="#edUserModal" >
                        <i class="fa fa-edit fa-2x"></i>
                    
                    </button>
                    <button type="button" class="btn btn-outline-danger" data-toggle="tooltip" data-placement="bottom" title="Eliminar"
                        data-uuid="{{ $user->id }}"
                        data-name="{{ $user->first_name }} {{$user->last_name}}"
                        data-toggle="modal"
                        data-target="#delUserModal">
                        <i class="fa fa-trash fa-2x"></i>
                    
                    </button>
                    @if(!$user->is_active)
                    <a class="btn btn-outline-success" data-toggle="tooltip" data-placement="bottom" title="Activar" href="{{ route('users.active', ['uuid' => $user->id]) }}">
                        <i class="fas fa-check fa-2x"></i>
                    </a>
                    @elseif ($user->is_active)
                    <a class="btn btn-outline-danger" data-toggle="tooltip" data-placement="bottom" title="Desactivar" href="{{ route('users.desactive', ['uuid' => $user->id]) }}">
                        <i class="fas fa-times fa-2x"></i>
                    </a>
                    @endif
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
            </div>
            
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('users') }}" method="POST" id="addUserModal" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">{{ __('Add User') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="userType">{{ __('User type') }}</label>
                        <select class="form-control" name="userType" id="userType" >
                            <option selected value="0">Cliente</option>
                            <option value="1">Administrador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fName">{{ __('Firstname') }}</label>
                        <input type="text" class="form-control" name="fName" id="fName" required />
                    </div>
                    <div class="form-group">
                        <label for="lName">{{ __('Lastname') }}</label>
                        <input type="text" class="form-control" name="lName" id="lName" required />
                    </div>
                    <div class="form-group">
                        <label for="email">{{ __('E-Mail Address') }}</label>
                        <input type="text" class="form-control" name="email" id="email" required />
                    </div>
                    <div class="form-group">
                        <label for="phone">{{ __('Phone') }}</label>
                        <input type="text" class="form-control" name="phone" id="phone" />
                    </div>
                    <div class="form-group">
                        <label for="password">{{ __('Password') }}</label>
                        <input type="password" class="form-control" name="password" id="password" required />
                    </div>
                    <div class="form-group">
                        <label for="avatar">{{ __('Avatar') }}</label>
                        <input type="file" class="form-control" name="avatar" id="avatar" />
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('Add User') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edUserModal" tabindex="-1" role="dialog" aria-labelledby="edUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('users.update') }}" method="POST" id="editUserModal" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="edUserModalLabel">{{ __('Edit User') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="userType">{{ __('User type') }}</label>
                        <select class="form-control" name="userType" id="userType" >
                            <option selected value="0">Cliente</option>
                            <option value="1">Administrador</option>
                        </select>
                    </div>
                    <input type="hidden" class="form-control" name="uuid" id="uuid" />
                    <div class="form-group">
                        <label for="fName">{{ __('Firstname') }}</label>
                        <input type="text" class="form-control" name="fName" id="fName" required />
                    </div>
                    <div class="form-group">
                        <label for="lName">{{ __('Lastname') }}</label>
                        <input type="text" class="form-control" name="lName" id="lName" required />
                    </div>
                    <div class="form-group">
                        <label for="email">{{ __('E-Mail Address') }}</label>
                        <input type="text" class="form-control" name="email" id="email" required />
                    </div>
                    <div class="form-group">
                        <label for="phone">{{ __('Phone') }}</label>
                        <input type="text" class="form-control" name="phone" id="phone" />
                    </div>
                    <div class="form-group">
                        <label for="password">{{ __('Password') }}</label>
                        <input type="password" class="form-control" name="password" id="password" />
                    </div>
                    <div class="form-group">
                        <label for="avatar">{{ __('Avatar') }}</label>
                        <input type="file" class="form-control" name="avatar" id="avatar" />
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('Edit User') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="delUserModal" tabindex="-1" role="dialog" aria-labelledby="delUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form action="{{ route('users') }}" method="POST">
                @csrf
                @method("delete")
                <div class="modal-header">
                    <h5 class="modal-title" id="delUserModalLabel">{{ __('Remove User') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    {{-- You're about to remove <b><span class="user-name"></span></b> user, want to proceed? --}}
                    <h4>{{ __('You are about to remove this user') }}</h4>
                    <h5 class="user-name my-2"></h5>
                    <h4 class="my-3">{{ __('Want to proceed?') }}</h4>
                    <input type="hidden" name="uuid" id="delUserUuid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Remove User') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>

    $('#delUserModal').on('show.bs.modal', function (e) {

        $modal = $(this);
        const button = $(e.relatedTarget);

        const name = button.data('name');
        const uuid = button.data('uuid');

        $modal.find('.user-name').text(name);
        $modal.find('#delUserUuid').val(uuid);
    });

    $('#edUserModal').on('show.bs.modal', function (e) {

        $modal = $(this);
        const button = $(e.relatedTarget);

        const fname = button.data('fname');
        const lname = button.data('lname');
        const email = button.data('email');
        const phone = button.data('phone');
        const tuser = button.data('tuser');
        const uuid = button.data('uuid');

        $modal.find('#uuid').val(uuid);
        $modal.find('#fName').val(fname);
        $modal.find('#lName').val(lname);
        $modal.find('#email').val(email);
        $modal.find('#phone').val(phone);
        $modal.find('#userType').val(tuser);

    });

</script>
@endsection