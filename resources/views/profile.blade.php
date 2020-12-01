@extends('layouts.app')

@section('content')
@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>@lang($message)</strong>
</div>
@endif

@if (count($errors) > 0)
<div class="alert alert-danger">
    <strong>{{ __('Whoops') }}!</strong> {{ __('There were some problems updating your profile') }}.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('User Settings') }}</div>
                <div class="card-body">
                    <form class="mt-4" action="{{ route('profile') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <table class="mb-4">
                            <tbody>
                                <tr>
                                    <td class="align-middle px-2">
                                        <img src="{{ asset('storage/' .  $user->id . '/avatars/' . $user->avatar) }}" id="avatarImage"
                                            class="border border-secondary rounded-circle img-fluid img-thumbnail picture-profile">
                                    </td>
                                    <td class="align-middle pl-2">
                                        <a class="btn btn-outline-secondary" href="#" role="button" id="updateAvatarButton">{{ __('Update Avatar') }}</a>
                                        <input type="file" class="form-control-file hidden" name="avatar"
                                            id="avatarFile" aria-describedby="fileHelp">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <label for="first_name">{{ __('Firstname') }}</label>
                            <input type="text" class="form-control" name="first_name" id="first_name"
                                value="{{ Auth::user()->first_name }}">
                        </div>
                        <div class="form-group">
                            <label for="last_name">{{ __('Lastname') }}</label>
                            <input type="text" class="form-control" name="last_name" id="last_name"
                                value="{{ Auth::user()->last_name }}">
                        </div>
                        <div class="form-group">
                            <label for="phone">{{ __('Phone') }}</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="123-4567"
                                value="{{ Auth::user()->phone }}">
                        </div>
                        <a class="btn btn-block text-danger" href="#" role="button" data-toggle="modal"
                            data-target="#passwordModal">{{ __('Change Password') }}</a>
                        <button type="submit" class="btn btn-success btn-block">{{ __('Save Settings') }}</button>
                        <a class="btn btn-danger btn-block" role="button" href="{{ route('logout') }}"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('password') }}" method="POST" id="updatePasswordModal">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">{{ __('Update Password') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="oldpass">{{ __('Old Password') }}</label>
                        <input type="password" class="form-control" name="oldpass" id="oldpass" required />
                    </div>
                    <div class="form-group">
                        <label for="newpass">{{ __('New Password') }}</label>
                        <input type="password" class="form-control" name="newpass" id="newpass" required />
                    </div>
                    <div class="form-group">
                        <label for="conpass">{{ __('Confirm Password') }}</label>
                        <input type="password" class="form-control" name="conpass" id="conpass" required />
                    </div>
                    <div class="text-center">
                        <span id="updatePasswordModalResponse"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('Update Password') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const readURL = function (input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.readAsDataURL(input.files[0]);
            reader.onload = function(e) {
                console.log(e);
                $('#avatarImage').attr('src', e.target.result);
            };
        }
    }

    $("#updateAvatarButton").click(function(e){
        e.preventDefault();
        $('#avatarFile').trigger('click');
    });

    $("#avatarFile").change(function() {
        readURL(this);
    });

    $("#updatePasswordModal").submit(function(e){
        e.preventDefault();
        
        $form = $(this);
        $resp = $("#updatePasswordModalResponse");

        $resp.text("");

        if($("#newpass").val() != $("#conpass").val()) {
            $resp.text("Confirm password must match");
        } else {
            $.ajax({
                url: $form.attr('action'),
                method: $form.attr('method'),
                data: $form.serialize()
            }).done(function(response) {
                if(response.error) {
                    if(typeof response.error != "string") {
                        let list = "";
                        
                        for (let err in response.error) {
                            const object = response.error[err];
                            for (let i = 0; i < object.length; i++) {
                                list += object[i] + "<br/>";
                            }
                        }
                    
                        $resp.html(list);
                    } else {
                        $resp.text(response.error);
                    }   
                } else {
                    $resp.text(response.success);
                    $form.trigger("reset");
                }
            });
        }
    });
</script>
@endsection