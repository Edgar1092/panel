@extends('admin-layout.app')

@section('content')
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Usuarios</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item active">Usuarios </li>
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
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a class="btn btn-outline-secondary" href="#!" role="button" data-toggle="modal" data-target="#userModal">{{ __('Add User') }}</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('users') }}" method="GET" >
                                <div class="form-group input-group">
                                    <input type="text" class="form-control" name="search" id="search" />
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" title="Buscar"><i class="fas fa-search"></i></button>
                                    </span>
                                    <span class="input-group-btn">
                                    <a class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Limpiar" href="{{ route('users') }}">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    </span>
                                    
                                </div>
                                
                            </form>
                        </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                                <tr>
                                <th>UID</th>
                                <th>Nombres y Apellidos</th>
                                <th>Email</th>
                                <th>Telefono</th>
                                <th>Perfil</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                                </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                <td>{{$user->uuid}}</td>
                                <td>{{$user->first_name}} {{$user->last_name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->phone}}</td>
                                <td>{{$user->is_admin ? 'Administrador' : 'Cliente'}}</td>
                                <td>
                                @if(!$user->is_active)
                                <span class="text-danger">Desactivado</span>
                                @else
                                <span class="text-success">Activado</span>
                                @endif
                                </td>
                                <td>
                                <!-- <button type="button" class="btn btn-outline-primary" title="Editar"
                                     >
                                    <i class="fas fa-edit"></i>
                                
                                </button> -->
                                <!-- <button type="button" class="btn btn-outline-danger" title="Eliminar"
                                    >
                                    <i class="fas fa-trash"></i>
                                
                                </button> -->
                                
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default">Acciones</button>
                                    <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item text-primary" href="#" data-uuid="{{ $user->id }}"
                                    data-fname="{{ $user->first_name }}"
                                    data-lname="{{ $user->last_name }}"
                                    data-email="{{ $user->email }}"
                                    data-phone="{{ $user->phone }}"
                                    data-tuser="{{ $user->is_admin }}"
                                    data-toggle="modal"
                                    data-target="#edUserModal">Editar</a>
                                    
                                    <a class="dropdown-item text-danger" href="#" data-uuid="{{ $user->id }}"
                                    data-name="{{ $user->first_name }} {{$user->last_name}}"
                                    data-toggle="modal"
                                    data-target="#delUserModal">Eliminar</a>
                                    
                                    @if(!$user->is_active)
                                    <a class="dropdown-item text-success"  href="{{ route('users.active', ['uuid' => $user->id]) }}">
                                        Activar
                                    </a>
                                    @elseif ($user->is_active)
                                    <a class="dropdown-item text-warning" href="{{ route('users.desactive', ['uuid' => $user->id]) }}">
                                        Desactivar
                                    </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('screens_admin', ['uuid' => $user->id]) }}">
                                        Pantallas
                                    </a>
                                    <a class="dropdown-item" href="{{ route('content_admin', ['uuid' => $user->id]) }}">
                                        Contenidos
                                    </a>
                                    <a class="dropdown-item" href="{{ route('playlist_admin', ['uuid' => $user->id]) }}">
                                        Playlist
                                    </a>
                                    </div>

                                </div>
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                           {{ $users->links() }} 
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
                        <select class="form-control" name="userType" id="aduserType" >
                            <option selected value="0">Cliente</option>
                            <option value="1">Administrador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fName">{{ __('Firstname') }}</label>
                        <input type="text" class="form-control" name="fName" id="adfName" required />
                    </div>
                    <div class="form-group">
                        <label for="lName">{{ __('Lastname') }}</label>
                        <input type="text" class="form-control" name="lName" id="adlName" required />
                    </div>
                    <div class="form-group">
                        <label for="email">{{ __('E-Mail Address') }}</label>
                        <input type="text" class="form-control" name="email" id="ademail" required />
                    </div>
                    <div class="form-group">
                        <label for="phone">{{ __('Phone') }}</label>
                        <input type="text" class="form-control" name="phone" id="adphone" />
                    </div>
                    <div class="form-group">
                        <label for="password">{{ __('Password') }}</label>
                        <input type="password" class="form-control" name="password" id="adpassword" required />
                    </div>
                    <div class="form-group">
                        <label for="avatar">{{ __('Avatar') }}</label>
                        <input type="file" class="form-control" name="avatar" id="adavatar" />
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