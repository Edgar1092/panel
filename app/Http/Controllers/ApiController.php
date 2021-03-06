<?php

namespace App\Http\Controllers;

use Auth;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string|min:15|max:36',
            'name' => 'required',
            'brand' => 'required',
            'serial' => 'required',
            'manufacturer' => 'required',
            'os' => 'required',
            'version' => 'required'
        ]);

        $user = Auth::guard('api')->user();

        if (!\App\Screen::where('uuid', '=', $request->uuid)->exists()) {    
            $screen = new \App\Screen([
                'uuid' => $request->uuid,
                'name' => $request->name,
                'brand' => $request->brand,
                'manufacturer' => $request->manufacturer,
                'os' => $request->os,
                'version' => $request->version,
                'serial' => $request->serial,
                'lat' => isset($request->lat) ? $request->lat : 0,
                'lng' => isset($request->lng) ? $request->lng : 0
            ]);
    
            $user->screens()->save($screen);
        } else if (!$user->screens()->firstWhere('uuid', $request->uuid)) {
            $screen = \App\Screen::where('uuid', '=', $request->uuid)->first();
            $user->screens()->save($screen);
        }
        
        if (\App\Screen::where('uuid', '=', $request->uuid)->exists()) { 
            $screen = \App\Screen::where('uuid', '=', $request->uuid)->first();
            $screen->lat = isset($request->lat) ? $request->lat : 0;
            $screen->lng = isset($request->lng) ? $request->lng : 0;
            $screen->save();
        }
        return response()->json([
            'screen' => $screen,
            'message' => 'Successfully registered'
        ]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string|min:15|max:36',
        ]);

        $user = Auth::user();
        $screen = $user->screens()->firstWhere('uuid', $request->uuid);
        
        return response()->json([
            'offline' => $screen ? $screen->offline : 0,
            'sync_at' => $screen ? $screen->sync_at : null
        ]);
    }

    public function content(Request $request)
    {
        $playlist = [];
        $playlistContent = [];

        $user = Auth::guard('api')->user();

        $current_time = strtotime(date("h:i:s"));

        $fechados=date("h:i:s");

        //$screen = \App\Screen::where('uuid', '=', $request->uuid)->where('user_id', $user->id)->first();
        $screen = $user->screens()->firstWhere('uuid', $request->uuid);
        $relation = \App\SchedulePlaylist::where('screen_id', '=', $screen->id)->get();


// var_dump('fechados',date("h:i:s"));
        foreach ($relation as $key => $value) {
            $schedule = $value->schedule()->first();

            $start = strtotime($schedule->init_at);
            $end = strtotime($schedule->ends_at);

            if ($current_time >= $start && $current_time <= $end) {
                $playlist = $value->playlist()->first();

                 // var_dump('tiempo',$playlist);
                break;
            }
        }

        if ($playlist) {
            $content = $playlist->playlistContent()->get();
    
            foreach ($content as $item) {
                $item = $item->content()->first();
                $file = asset('storage/' .  $user->id . '/content/' . $item->name);
                
                if ($item->type == 'video') {
                    $compressed = [];
                    $formatted = [];
                    
//                    if (file_exists($file . '.webm')) {
if (Storage::disk('public')->exists($user->id . '/content/' . $item->name . '.webm')) {
                        $compressed = [
                            'mime' => 'video/webm',
                            'file' => $file . '.webm'
                        ];
                    }

//                    if (file_exists($file . '.x264.mp4')) {
if (Storage::disk('public')->exists($user->id . '/content/' . $item->name . '.x264.mp4')) {
                        $formatted = [
                            'mime' => 'video/mp4',
                            'file' => $file . '.x264.mp4'
                        ];
                    }

                    $playlistContent[] = [
                        'type' => $item->type,
                        'webm' => $compressed,
                        'x264' => $formatted,
                        'mp4' => [
                            'mime' => $item->mime,
                            'file' => $file
                        ]
                    ];
                } else {
                    $playlistContent[] = [
                        'type' => $item->type,
                        'mime' => $item->mime,
                        'file' => $file
                    ];
                }
            }
        }

        return Response::json([
            'content' => $playlistContent,
            'playlist' => $playlist ? [
                'name' => $playlist->name,
                'interval' => $playlist->interval
            ] : []
        ]);
    }

    public function download(Request $request)
    {
        $downloadContent = [];
        
        $user = Auth::guard('api')->user();
        $screen = $user->screens()->firstWhere('uuid', $request->uuid);

        $schedules = $screen->schedules()->get();

        foreach ($schedules as $item) {
            $playlist = $item->playlist()->first();
            $schedule = $item->schedule()->first();

            $content = $playlist->playlistContent()->get();
            
            foreach ($content as $item) {
                $item =  $item->content()->first();
                $playlistContent[] = [
                    'type' => $item->type,
                    'name' => $item->name,
                    'mime' => $item->mime,
                    'file' => asset('storage/' .  $user->id . '/content/' . $item->name)
                ];
            }
            
            $downloadContent[] = [
                'playlist' => [
                    'name' => $playlist->name,
                    'interval' => $playlist->interval
                ],
                'schedule' => [
                    'init_at' => $schedule->init_at,
                    'ends_at' => $schedule->ends_at,
                ],
                'content' => $playlistContent
            ];
        }

        return Response::json($downloadContent);
    }

    public function update(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string|min:15|max:36',
        ]);

        $current_time = Carbon::now()->toDateTimeString();

        $user = Auth::user();
        $screen = $user->screens()->firstWhere('uuid', $request->uuid)->update([
            'sync_at' => $current_time
        ]);
        
        return response()->json([
            'date' => $current_time
        ]);
    }


     public function loginCode(Request $request){



        // $credentials = request(['email', 'password']);
        // // $credentials = ['email'=>$request->email, 'password'=>$request->password, 'is_active'=> 1];
        
        // if(!Auth::attempt($credentials)) {
        //     return response()->json([
        //         'message' => 'Unauthorized'
        //     ], 401);
        // }

        // $user = $request->user();

        // return "hola";
  

        if(Screen::where('uuid',$request->uuid)->exists){

            $consulta=Screen::where('uuid',$request->uuid)->first();
            $user=User::find($consulta->user_id);
        
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            
            if ($request->remember_me) {
                $token->expires_at = Carbon::now()->addWeeks(1);
            }

            $token->save();
            
            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'user' => $user,
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);
        }else{
            // return'culo';
               return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

    }
}

