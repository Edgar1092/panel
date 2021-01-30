<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Screen;
use App\Content;
use App\Playlist;
use Validator;
use Hash;
use File;
use Carbon\Carbon;
use App\Rules\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Schedule;
use App\ScheduleUser;
use App\SchedulePlaylist;
use App\UserScreens;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile', compact('user', $user));
    }
    public function store(Request $request)
    {
        $request->validate([
            'fName' => 'required|string',
            'lName' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
            'phone' => 'numeric',
            'userType' => 'required|boolean',
        ]);
        if ($request->avatar) {
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
        }
        $user = new User;
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token=substr(str_shuffle(str_repeat($pool, 5)), 0, 15);
        $user->uuid = $token;
        $user->first_name = $request->fName;
        $user->last_name = $request->lName;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->phone = $request->phone;
        $user->is_admin = $request->userType;
        $user->is_promotor = $request->is_promotor;
        if ($request->avatar) {
            $avatarName = time() . '.' . request()->avatar->getClientOriginalExtension();
            $request->avatar->storeAs( $user->id . '/avatars', $avatarName);
            $user->avatar = $avatarName;
        }
        $user->save();
        
        return back()->with('success', 'You have successfully register user.');
    }
    public function edit(Request $request)
    {
        $request->validate([
            'fName' => 'required|string',
            'lName' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'numeric',
            'userType' => 'required|boolean',
        ]);
        if ($request->avatar) {
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
        }
        if ($request->password) {
            $request->validate([
                'password' => 'string',
            ]);
        }
        $user = User::find($request->uuid);

        if($user->uuid == "" || $user->uuid == null){
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $token=substr(str_shuffle(str_repeat($pool, 5)), 0, 15);
            $user->uuid = $token;
        }
        $user->first_name = $request->fName;
        $user->last_name = $request->lName;

        if($request->email != $user->email){
            $user->email = $request->email; 
        }
        
        if($request->password){
          $user->password = bcrypt($request->password);  
        }
        
        $user->phone = $request->phone;
        $user->is_admin = $request->userType;
        $user->is_promotor = $request->is_promotor;
        if ($request->avatar) {
            $avatarName = time() . '.' . request()->avatar->getClientOriginalExtension();
            $request->avatar->storeAs( $user->id . '/avatars', $avatarName);
            $user->avatar = $avatarName;
        }
        $user->save();
        
        return back()->with('success', 'You have successfully update user.');
    }
    public function update(Request $request)
    {
        $request->validate([
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);

        if ($request->phone) {
            $request->validate([
                'phone' => ['required', new PhoneNumber],
            ]);
        } else {
            $request->phone = "";
        }

        $user = Auth::user();

        if ($request->avatar) {
            $avatarName = time() . '.' . request()->avatar->getClientOriginalExtension();
            $request->avatar->storeAs( $user->id . '/avatars', $avatarName);
        }
        if($user->uuid == "" || $user->uuid == null){
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $token=substr(str_shuffle(str_repeat($pool, 5)), 0, 15);
            $user->uuid = $token;
        }
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;

        if ($request->avatar)
            $user->avatar = $avatarName;

        $user->save();

        return back()->with('success', 'You have successfully updated profile.');
    }

    public function password(Request $request)
    {
        if (Auth::Check()) {
            if ($request->ajax() && $request->method() == "POST") {

                $messages = [
                    'oldpass.required' => 'Please enter the old password',
                    'newpass.required' => 'Please enter the new password',
                    'conpass.required' => 'Please confirm the new password',
                    'newpass.same' => 'New password does not match',
                    'conpass.same' => 'Confirm password does not match',
                ];

                $validator = Validator::make($request->All(), [
                    'oldpass' => 'required',
                    'newpass' => 'required|same:newpass',
                    'conpass' => 'required|same:newpass',
                ], $messages);

                if ($validator->fails()) {
                    return response()->json([
                        'error' => $validator->getMessageBag()->toArray()
                    ]);
                } else {
                    $current_password = Auth::User()->password;

                    if (Hash::check($request->oldpass, $current_password)) {
                        $user = Auth::user();
                        $user->password = Hash::make($request->newpass);
                        $user->save();

                        return response()->json([
                            'success' => 'Password updated'
                        ]);
                    } else {
                        return response()->json([
                            'error' => 'Please enter correct current password'
                        ]);
                    }
                }
            } else {
                return redirect()->route('profile');
            }
        } else {
            return redirect()->to('/');
        }
    }

      public function getScreens(Request $request)
    {
        $user = Auth::user();
        $horaActual = date("H:i:s");
        $hora = date("H");
        $min = date("i");
        if($user->is_admin){
            $u = User::find($request->uuid);
            $screens = $u->screens()->orderBy('created_at', 'asc')->get();
            if($u->is_promotor){
              $screensPromotor = User::join('user_screens', 'user_screens.user_id', '=', 'users.id')
              ->join('screens', 'user_screens.screen_id', '=', 'screens.id')
              ->where('users.id',$request->uuid )
              ->select('screens.*')
              ->orderBy('screens.created_at', 'asc')
              ->get();
            }else{
                $screensPromotor = [];
            }
            
            // $screens = Screen::with('user')->orderBy('created_at', 'asc')->get();
            $usuarios = User::where('is_active',1)->orderBy('created_at', 'asc')->get();
            $screensOn = $u->screens()->where('offline',0)->count();
            $screensOff = $u->screens()->where('offline',1)->count();
            if($min>=30){
                $init = $hora.":30:00";
                $ends = $hora.":59:00";
            }else{
                $init = $hora.":00:00";
                $ends = $hora.":29:00";
            }
            $schActivo = Schedule::where('init_at',$init)->where('ends_at',$ends)->first();
            foreach ($screens as $key => $value) {
                $tienePlaylist = SchedulePlaylist::where('screen_id',$value->id)
                ->where('schedule_id',$schActivo->id)
                ->first();
                if($tienePlaylist){
                    $pl = Playlist::find($tienePlaylist->playlist_id);
                    $screens[$key]['reproduce'] = 1;
                    $screens[$key]['playlist_reproduce'] = $pl;
                }else{
                    $screens[$key]['reproduce'] = 0;
                    $screens[$key]['playlist_reproduce'] = array();
                }
            }
            foreach ($screensPromotor as $key => $value) {
                $tienePlaylist = SchedulePlaylist::where('screen_id',$value->id)
                ->where('schedule_id',$schActivo->id)
                ->first();
                if($tienePlaylist){
                    $pl = Playlist::find($tienePlaylist->playlist_id);
                    $screensPromotor[$key]['reproduce'] = 1;
                    $screensPromotor[$key]['playlist_reproduce'] = $pl;
                }else{
                    $screensPromotor[$key]['reproduce'] = 0;
                    $screensPromotor[$key]['playlist_reproduce'] = array();
                }
            }
            // print_r($screensPromotor->toArray());
            // print_r($screens->toArray());
            return view('admin.screens', [
                'user' => $user,
                'userSelected'=>$u,
                'screens' => $screens,
                'screensPromotor' => $screensPromotor,
                'users' => $usuarios,
                'screensOn' =>$screensOn,
                'screensOff' => $screensOff
            ]);
        }else{
            $screens = $user->screens()->orderBy('created_at', 'asc')->get();
            if($user->is_promotor){
                $screensPromotor = User::join('user_screens', 'user_screens.user_id', '=', 'users.id')
                ->join('screens', 'user_screens.screen_id', '=', 'screens.id')
                ->where('users.id',$user->id )
                ->select('screens.*')
                ->orderBy('screens.created_at', 'asc')
                ->get();
              }else{
                  $screensPromotor = [];
              }
            $usuarios = [];

            if($min>=30){
                $init = $hora.":30:00";
                $ends = $hora.":59:00";
            }else{
                $init = $hora.":00:00";
                $ends = $hora.":29:00";
            }
            $schActivo = Schedule::where('init_at',$init)->where('ends_at',$ends)->first();
            foreach ($screens as $key => $value) {
                $tienePlaylist = SchedulePlaylist::where('screen_id',$value->id)
                ->where('schedule_id',$schActivo->id)
                ->first();
                if($tienePlaylist){
                    $pl = Playlist::find($tienePlaylist->playlist_id);
                    $screens[$key]['reproduce'] = 1;
                    $screens[$key]['playlist_reproduce'] = $pl;
                }else{
                    $screens[$key]['reproduce'] = 0;
                    $screens[$key]['playlist_reproduce'] = array();
                }
            }
            foreach ($screensPromotor as $key => $value) {
                $tienePlaylist = SchedulePlaylist::where('screen_id',$value->id)
                ->where('schedule_id',$schActivo->id)
                ->first();
                if($tienePlaylist){
                    $pl = Playlist::find($tienePlaylist->playlist_id);
                    $screensPromotor[$key]['reproduce'] = 1;
                    $screensPromotor[$key]['playlist_reproduce'] = $pl;
                }else{
                    $screensPromotor[$key]['reproduce'] = 0;
                    $screensPromotor[$key]['playlist_reproduce'] = array();
                }
            }
            return view('screens', [
                'user' => $user,
                'screens' => $screens,
                'screensPromotor' => $screensPromotor,
                'users' => $usuarios
            ]);
        }
        // var_dump($screens);
        
    }

    public function getUsers(Request $request)
    {
        if(!Auth::user()){
            return redirect('login');
        }
        $user = Auth::user();
        if($user->is_admin){
            if($request->search){
                if(strtolower($request->search) == 'administrador' || strtolower($request->search) == 'administradores'){
                    $query = User::where('is_admin', 1);
                }else if(strtolower($request->search) == 'cliente' || strtolower($request->search) == 'clientes'){
                    $query = User::where('is_admin', 0);
                }else if(strtolower($request->search) == 'activo' || strtolower($request->search) == 'activos'){
                    $query = User::where('is_active', 1);
                }else if(strtolower($request->search) == 'desactivo' || strtolower($request->search) == 'desactivos' || strtolower($request->search) == 'inactivo' || strtolower($request->search) == 'inactivos'){
                    $query = User::where('is_active', 0);
                }else{
                 $query = User::where('first_name', 'LIKE', '%'.$request->search.'%')
                                ->orWhere('last_name', 'LIKE', '%'.$request->search.'%')
                                ->orWhere('email', 'LIKE', '%'.$request->search.'%')
                                ->orWhere('phone', 'LIKE', '%'.$request->search.'%');   
                }

                $usuarios = $query->where('is_promotor', 0)->orderBy('created_at', 'asc')->paginate(10); 
                
            }else{
              $usuarios = User::where('is_promotor', 0)->orderBy('created_at', 'asc')->paginate(10);  
            }
            
        }else{
            $usuarios = [];
        }
        // var_dump($usuarios);
        return view('admin.users', [
            'user' => $user,
            'users' => $usuarios
        ]);
    }
    public function getUsersPromotores(Request $request)
    {
        if(!Auth::user()){
            return redirect('login');
        }
        $user = Auth::user();
        if($user->is_admin){
            if($request->search){
                if(strtolower($request->search) == 'administrador' || strtolower($request->search) == 'administradores'){
                    $query = User::where('is_admin', 1);
                }else if(strtolower($request->search) == 'cliente' || strtolower($request->search) == 'clientes'){
                    $query = User::where('is_admin', 0);
                }else if(strtolower($request->search) == 'activo' || strtolower($request->search) == 'activos'){
                    $query = User::where('is_active', 1);
                }else if(strtolower($request->search) == 'desactivo' || strtolower($request->search) == 'desactivos' || strtolower($request->search) == 'inactivo' || strtolower($request->search) == 'inactivos'){
                    $query = User::where('is_active', 0);
                }else{
                 $query = User::where('first_name', 'LIKE', '%'.$request->search.'%')
                                ->orWhere('last_name', 'LIKE', '%'.$request->search.'%')
                                ->orWhere('email', 'LIKE', '%'.$request->search.'%')
                                ->orWhere('phone', 'LIKE', '%'.$request->search.'%');   
                }

                $usuarios = $query->where('is_promotor', 1)->orderBy('created_at', 'asc')->paginate(10); 
                
            }else{
              $usuarios = User::where('is_promotor', 1)->orderBy('created_at', 'asc')->paginate(10);  
            }
            
        }else{
            $usuarios = [];
        }
        // var_dump($usuarios);
        return view('admin.users_promotores', [
            'user' => $user,
            'users' => $usuarios
        ]);
    }
    public function delete(Request $request)
    {
        $request->validate([
            'uuid' => 'required'
        ]);

        $user = User::find($request->uuid);
        $user->delete();

        return back()->with('success', "You have successfully removed the user")->with('color', 'danger');
    }
    public function active(Request $request)
    {

        $user = User::find($request->uuid);
        $user->is_active = 1;
        $user->save();

        return back()->with('success', "You have successfully active the user")->with('color', 'success');
    }
    public function desactive(Request $request)
    {

        $user = User::find($request->uuid);
        $user->is_active = 0;
        $user->save();

        return back()->with('success', "You have successfully desactive the user")->with('color', 'success');
    }
    public function addScreens(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string|min:15|max:36',
            'name' => 'required|string|min:2|max:225',
        ]);

        $user = Auth::user();
        
        $screen = new \App\Screen([
            'uuid' => $request->uuid,
            'name' => $request->name
        ]);

        if($user->is_admin){
           $userSelected = User::find($request->userSelected);
           $userSelected->screens()->save($screen);  
        }else{
          $user->screens()->save($screen);  
        }
        

        return back()->with('success', "You have successfully added the screen: $request->name.");
    }

    public function assignScreens(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string|min:15|max:36',
        ]);

        $user = Auth::user();
        $screen_u = Screen::where("uuid",$request->uuid)->where("user_id",$request->userSelected)->count();
        if($screen_u == 0){
            $screen = Screen::where("uuid",$request->uuid)->first();
            if($screen){
                $screen_us = UserScreens::where("screen_id",$screen->id)->where("user_id",$request->userSelected)->count();
                if($screen_us == 0){
                    
                        $user_screens = UserScreens::create([
                            'user_id' => $request->userSelected,
                            'screen_id' => $screen->id
                        ]);
                    
                }else{
                    return back()->with('success', "La pantalla ya se encuentra asignada a este usuario");
                }
            }else{
                return back()->with('error', "La pantalla que intenta asignar no existe");
            }
            
        }else{
            return back()->with('success', "La pantalla ya se encuentra asignada a este usuario");
        }
        
        return back()->with('success', "La pantalla fue asignada correctamente");
    }

    public function delScreens(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string|min:15|max:36'
        ]);

        $user = Auth::user();
        if($user->is_admin){
            $userSelected = User::find($request->idUser);
            $screen = $userSelected->screens()->firstWhere('uuid', $request->uuid);
        }else{
            $screen = $user->screens()->firstWhere('uuid', $request->uuid);
        }
        $screen->delete();

        return back()->with('success', "You have successfully removed the screen: $screen->name.")->with('color', 'danger');
    }

    public function uptScreens(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string|min:15|max:36'
        ]);
        
        $user = Auth::user();
        
        if($request->action == "info") {
            $request->validate([ 'name' => 'required|string|min:1|max:150' ]);
            $values = [ 'name' => $request->name ];
        } else {
            $values = isset($request->force) || $request->offline == 1 ? [
                'sync_at' => Carbon::now()->toDateTimeString(), 'offline' => 1
            ] :  [ 'offline' => 0 ];
        }
        if($user->is_admin){
            $userSelected = User::find($request->idUser);
            $userSelected->screens()->firstWhere('uuid', $request->uuid)->update($values);
        }else{
          $user->screens()->firstWhere('uuid', $request->uuid)->update($values);  
        }
        
        return back()->with('success', "You have successfully updated the current screen");
    } 
    
    
    public function getScreensApi(Request $request)
    {
        $user=User::where('id',$request->id)->first();
        if($user->is_admin){
            $screens = Screen::where('user_id',$request->id)->orderBy('created_at', 'asc')->get();
        }else{
            $screens = Screen::orderBy('created_at', 'asc')->get();          
        }

        return response()->json([
        
            'user'  => $user,
            'screens'    => $screens,
    
        ]);

    }

    public function getOneScreensApi(Request $request)
    {
      
        $screen = Screen::where('id',$request->id)->first();

        return response()->json([
        
            'screen'    => $screen,
    
        ]);

    }

    

    public function addScreensApi(Request $request)
    {
       

        $screen = Screen::create([
            'uuid'    => $request->uuid,
            'name'     => $request->name,
            'serial'  => $request->serial,
            'lng'  => $request->lng,
            'lat'  => $request->lat,
            'brand'  => $request->brand,
            'manufacturer'  => $request->manufacturer,
            'os'  => $request->idPago,
            'version'  => $request->idPago,
            'user_id'  => $request->user_id,
           
        ]);


        return response()->json([
        
            'msj'  => "You have successfully added the screen: $request->name.",
           
    
        ]);

    }

    public function uptScreensApi(Request $request)
    {
        // $request->validate([
        //     'uuid' => 'required|string|min:15|max:36'
        // ]);

        // $user = Auth::user();

        // if($request->action == "info") {
        //     $request->validate([ 'name' => 'required|string|min:1|max:150' ]);
        //     $values = [ 'name' => $request->name ];
        //     $screen= Screen::find($request->id);

        //     $screen->name=$request->name;
        //     $screen->save();
        //     return response()->json([
        
        //         'msj'  => "You have successfully updated the current screen",
               
        
        //     ]);
     
        // } else {
            // $values = isset($request->force) || $request->offline == 1 ? [
            //     'sync_at' => Carbon::now()->toDateTimeString(), 'offline' => 1
            // ] :  [ 'offline' => 0 ];

            $screen= Screen::find($request->id);

            $screen->sync_at=Carbon::now()->toDateTimeString();
            $screen->offline==1 ? $screen->offline=0 : $screen->offline=1;
         
            $screen->save();
            return response()->json([
        
                'msj'  => "You have successfully updated the current screen",
               
        
            ]);
        // }
        

    
    } 
    
    public function delScreensApi(Request $request)
    {
   
        $screen=find($request->id);
        $screen->delete();

        return response()->json([
        
            'msj'  => "You have successfully removed the screen: $screen->name.",
           
    
        ]);

    }

    public function getPlaylistsApi(Request $request)
    {
        // $user = User::find($request->id);
        // $options = $user->playlists()->get();
        // $screens = $user->screens()->orderBy('created_at', 'asc')->get();


        // return response()->json([
        
        //     'user' => $user,
        //     'list' => $options,
        //     'screens' => $screens,

        // ]);

        $user = User::find($request->id);
        $options = $user->playlists()->select('id','name','interval')->get();
		$screens = $user->screens()->orderBy('created_at', 'asc')->get();
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
            'user' => $u,
            'list' => $options,
            'screens' => $screens,
            'result' =>$result 
        ]);

      
    }


      public function updatePlaylistApi(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:225',
        ]);

        $user = User::find($request->idUsuario);

        $playlist = $user->playlists()->firstWhere('id', $request->id)->update([
            'name' => $request->name,
            // 'interval' => $request->interval ?? 10
        ]);

        return response()->json([
        
            'msj' => "You have successfully updated the playlist",

        ]);

    }

    public function showUserApi(Request $request){
        $usuario=User::find($request->id);

        return response()->json([
        
            'usuario' => $usuario,

        ]);

    }


    public function updateUserApi(Request $request)
    {
  

        $user = User::find($request->id);

        if ($request->avatar) {
            $avatarName = time() . '.' . request()->avatar->getClientOriginalExtension();
            $request->avatar->storeAs( $user->id . '/avatars', $avatarName);
        }

        $user->first_name = $request->first_name;
        $user->email = $request->email;
     
        if($request->password!=''){
            $request->password= Hash::make($request->password);
        }


        // if ($request->avatar)
        //     $user->avatar = $avatarName;

        $user->save();

        return response()->json([
        
            'msj' => "Update success",

        ]);
    }

      public function viewPlaylistApi(Request $request)
    {
        $user = User::find($request->idUser);

        $playlist = $user->playlists()->firstWhere('id', $request->id);
        $content = $playlist->playlistContent()->get();

        $playlistContent = [];
        $count = [ 'images' => 0, 'videos' => 0 ];

        foreach ($content as $item) {
            $item = $item->content()->first();
            $playlistContent[] = $item;

            switch ($item->type) {
                case "image": $count['images']++; break;
                case "video": $count['videos']++; break;
            }
        }

        return response()->json([
        
            'user' => $user,
            'playlist' => $playlist,
            'content' => $playlistContent,
            'count' => $count

        ]);

    }

    public function viewPlaylistAllApi(Request $request)
    {
        $user = User::find($request->idUser);

        // $playlist = $user->playlists()->firstWhere('id', $request->id);
        $content = Content::where('user_id',$request->idUser)->get();
        // $content = $playlist->playlistContent()->get();

        $playlistContent = [];
        $count = [ 'images' => 0, 'videos' => 0 ];

        foreach ($content as $item) {
            // $item = $item->content()->first();
            $playlistContent[] = $item;

            switch ($item->type) {
                case "image": $count['images']++; break;
                case "video": $count['videos']++; break;
            }
        }

        return response()->json([
        
            'user' => $user,
            // 'playlist' => $playlist,
            'content' => $playlistContent,
            'count' => $count

        ]);

    }

      public function viewPlaylistScreenApi(Request $request)
    {
        // $user = User::find($request->idUser);
        // $screen= Screen::find($request->id);
        // $playlist = Playlist::where('user_id',$request->idUser);
        // // $content = $playlist->playlistContent()->get();

        // // $playlistContent = [];
        // // $count = [ 'images' => 0, 'videos' => 0 ];

        // // foreach ($content as $item) {
        // //     $item = $item->content()->first();
        // //     $playlistContent[] = $item;

        // //     switch ($item->type) {
        // //         case "image": $count['images']++; break;
        // //         case "video": $count['videos']++; break;
        // //     }
        // // }

        // return response()->json([
        //     'Screen' => $screen,
        //     'user' => $user,
        //     'playlist' => $playlist,
        //     // 'content' => $playlistContent,
        //     // 'count' => $count

        // ]);

        $user = User::find($request->idUser);
        $count = [ 'images' => 0, 'videos' => 0 ];
if($request->id!=''){
        $screen = $user->screens()->firstWhere('uuid', $request->id);
        $actives = $screen->schedules()->get();
        $content = $user->contents()->get();
        $playlists = $user->playlists()->get();
        $schedules = $user->schedules()->get();

        /**
         * FIX WITHOUT BACKOFFICE ALL SCHEDULES
         */
        
        if (count($schedules) < 1) {
            for ($i=1; $i <= 48; $i++) {
                DB::table('schedule_users')->insert([
                    'schedule_id' => $i,
                    'user_id' => $user->id,
                ]);
            }
        }
        
        /**
         * END OF FIX
         */

        $userSchedules = [];
        $sectionSchedules = [
            'morning' => array(),
            'afternoon' => array(),
            'night' => array()
        ];

        foreach ($schedules as $schedule) {
            $item = $schedule->schedule()->first();

            $userSchedules[] = $item;

            if ($item->id >= 15 && $item->id <= 29) {
                $sectionSchedules['morning'][] = $item;
            }
            else if ($item->id >= 30 && $item->id <= 44) {
                $sectionSchedules['afternoon'][] = $item;
            }
            else {
                $sectionSchedules['night'][] = $item;
            }
        }
    }
        $playlists = $user->playlists()->get();
        foreach ($playlists as $playlist) {
            $content = $playlist->playlistContent()->get();
            foreach ($content as $item) {
                $item = $item->content()->first();
                $playlistContent[] = $item;
                switch ($item->type) {
                    case "image": $count['images']++; break;
                    case "video": $count['videos']++; break;
                }
            }
        }

        // return view('playlist.screen', [
        //     'user' => $user,
        //     'screen' => $screen,
        //     'content' => $content,
        //     'playlists' => $playlists,
        //     'schedules' => $userSchedules,
        //     'sectionSchedules' => $sectionSchedules,
        //     'actives' => $actives,
        //     'count' => $count
        // ]);
if($request->id!=''){
    return response()->json([
        'user' => $user,
      'screen' => $screen,
      'content' => $content,
      'playlists' => $playlists,
      'schedules' => $userSchedules,
      'sectionSchedules' => $sectionSchedules,
      'actives' => $actives,
      'count' => $count

  ]);
}else{
    return response()->json([
        'user' => $user,
   
      'playlists' => $playlists,
  

  ]);  
}
      

        

    }


    


    public function delPlaylistApi(Request $request)
    {
        

        $user = User::find($request->idUser);
        

        // foreach ($content as $index) {
            \App\PlaylistContent::where('content_id', $request->id)->delete();
            $user->contents()->where('id', $request->id)->delete();
        // }

        return response()->json([
            'msj' => 'Selected content was successfully removed!',
          

      ]);
    }


    public function addScheduleApi(Request $request)
    {
        $array = [];
        $user = User::find($request->idUsuario);

        $screen = $user->screens()->firstWhere('uuid', $request->id);
        $screen->schedules()->delete();

        if ($request->action === "update") {
            if ($request->playlistid !== 'none') {
                $schedules = \App\Schedule::all();
                foreach($schedules as $schedule) {
                    $array[] = new \App\SchedulePlaylist([
                        'fulltime' => false,
                        'screen_id' => $screen->id,
                        'playlist_id' => $request->playlistid,
                        'schedule_id' => $schedule->id,
                    ]);
                }
            }

            $screen->schedules()->saveMany($array);
        }

     return response()->json(['message' =>"Successfully Fulltime added"]);
    }

    public function addPartialScheduleApi(Request $request)
    {
        $array = [];
        $user = Auth::user();

        $screen = $user->screens()->firstWhere('uuid', $request->uuid);


        if ($request->action === "update") {
            if ($request->fulltimePlaylist !== 'none') {

                $start = $request->start;
                $end = $request->end;
                $schedules = \App\Schedule::all();
                foreach($schedules as $schedule) {
                    if($schedule->init_at >= $start && $schedule->ends_at <=$end )


                     $screen->schedules()->where('screen_id', $screen->id)->where('schedule_id',$schedule->id)->update(['playlist_id' =>$request->fulltimePlaylist]);
                }
                return response()->json(['message' => "Successfully updated Schedule"]);
            }
        }

     return response()->json(['message' => "Error occurs"]);
    }


    public function delScheduleApi(Request $request)
    {
        $array = [];
        $user = User::find($request->idUsuario);

        $screen = $user->screens()->firstWhere('uuid', $request->id);
        $screen->schedules()->delete();

   

     return response()->json(['message' =>"Successfully Schedule delete"]);
    }


    public function deletePlaylistApi(Request $request)
    {
        

        $user = User::find($request->idUser);

        $playlist = $user->playlists()->firstWhere('id', $request->id);
        $playlist->delete();

        return response()->json([
            'msj' => 'Removed!',
          

      ]);
    }

    public function asignarContentPlaylistApi(Request $request)
    {
        

        $user = User::find($request->idUser);

        DB::table('playlist_contents')->insert([
            'playlist_id' => $request->playlist_id,
            'user_id' => $user->id,
        ]);

        // $playlist = $user->playlists()->firstWhere('id', $request->id);
        // $playlist->delete();

        return response()->json([
            'msj' => 'Success!',
          

      ]);
    }


    public function setContentApi(Request $request)
    {
     

        if ($request->hasFile('file')) {

            $user = User::find($request->idUser);
            $file = $request->file('file');

            //$allowedfileExtension=['pdf','jpg','png','docx'];

            $array ;

                
                $ex = strtolower($file->getClientOriginalExtension());
                $ext = explode("?",$ex);
                $extension = $ext[0];
                $filename = time().".".$extension;
                //$check = in_array($extension, $allowedfileExtension);

                if (!\App\Content::where('name', '=', $filename)->where('user_id', $user->id)->exists()) {
                    $file->storeAs($user->id . '/content', $filename);

                    if (strpos($file->getMimeType(), 'video') !== false)
                        prepareVideo($user->id . '/content/' . $filename);

                    $array= new \App\Content([
                        'name' => $filename,
                        'type' => $extension,
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ]);
                } else {
                    $array = \App\Content::where('name', $filename)
                        ->where('type', $extension)
                        ->where('mime', $file->getMimeType())
                        ->where('size', $file->getSize())
                        ->first();
                }


            //$content = new \App\Content($array);
            $user->contents()->save($array);

            // if (is_numeric($request->playlist)) {
            //     $arrayPlaylist ;
            //     $playlist = $user->playlists()->select('id','name','interval')->firstWhere('id', $request->playlist);

            //         if (!\App\PlaylistContent::where('content_id', $array->id)->where('playlist_id', $playlist->id)->exists()) {
            //             $arrayPlaylist=  new \App\PlaylistContent([
            //                 'type' => $array->type,
            //                 'content_id' => $array->id,
            //                 'start_at' => 0,
            //                 'end_at' => 0
            //             ]);

            //     }

            //     $playlist->playlistContent()->save($arrayPlaylist);
            // }

            return response()->json([
                'message'=> 'Successfully added']);
        } else {
            return response()->json([
                'error'=>true,'message'=>'Unable to load images']);;
        }
    }

}
