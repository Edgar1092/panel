<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use File;
class loginController extends Controller
{

    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);

        if ($request->phone) {
            $request->validate([
                'phone' => ['required'],
            ]);
        } else {
            $request->phone = "";
        }

        $user = Auth::user();

        if ($request->avatar) {
            $avatarName = time() . '.' . request()->avatar->getClientOriginalExtension();
            $request->avatar->storeAs( $user->id . '/avatars', $avatarName);
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;

        if ($request->avatar)
            $user->avatar = $avatarName;

        $user->save();

        return  response()->json(['message' => 'You have successfully updated profile.']);
    }

    public function password(Request $request)
    {
        $request->validate([
            'oldpass' => 'required',
            'newpass' => 'required',
            'conpass' => 'required|same:newpass',
        ]);

                    $current_password = Auth::User()->password;

                    if (Hash::check($request->oldpass, $current_password)) {
                        $user = Auth::user();
                        $user->password = Hash::make($request->newpass);
                        $user->save();

                        return response()->json([
                            'message' => 'Password updated'
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Please enter correct current password'
                        ]);
                    }


    }


    public function updatePhoto(Request $request)
    {
        $request->validate([
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();
        if ($request->avatar) {
            $avatarName = time() . '.' . request()->avatar->getClientOriginalExtension();
            $request->avatar->storeAs( $user->id . '/avatars', $avatarName);
        }

        if ($request->avatar)
            $user->avatar = $avatarName;

        $user->save();

        return  response()->json(['message'=>'You have successfully updated profile.']);
    }


    public function user(Request $request)
    {


        $user= $user = Auth::user();




        $result = File::exists(asset('storage/' .  $user->id . '/avatars/' . $user->avatar));
        if(!$result){
            $u = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'file' => 'https://adhook.es/wp-content/uploads/2020/07/logo-ad.png'
            ];
        } else{
            $u = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'file' => asset('storage/' .  $user->id . '/avatars/' . $user->avatar)
            ];
        }

        return response()->json([
            'error'  =>false,
            'user'  => $u,
        ]);
    }


    public function login(Request $request)
    {


        $validatedData =  request(['email', 'password']);
        if(Auth::attempt($validatedData)) {
            $user = $request->user();
            Auth::login($user,true);
            $tokenResult = $user->createToken('API_Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            return response()->json([
                'error'  =>false,
                'user'  => $user,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);

        }else{
            return response()->json([
                'error'    =>true,
                'message' => 'Unauthorized'
            ], 401);
        }
    }


    public function register(Request $request)
    {


        $user = User::where('email', '=', $request->email)->first();
        if ($user === null) {
            $user = new User;
            $user->first_name = $request->fName;
            $user->last_name = $request->lName;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();
            $tokenResult = $user->createToken('API_Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();
            return response()->json([
                'error'  =>false,
                'user'  => $user,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer']);
        } else{
            return response()->json([
                'error'  =>true,
                'message' => "Email already Exist"]);
        }

    }


    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();

        $user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }


}
