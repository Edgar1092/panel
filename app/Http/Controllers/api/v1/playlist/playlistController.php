<?php

namespace App\Http\Controllers\api\v1\playlist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use App\Screen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use File;
class playlistController extends Controller
{

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

         $tem =$user->screens()->save($screen);

        return response()->json([
            'error'  =>false,
            'Playlist' => $tem,
        ]);
    }

    public function updateScreen(Request $request)
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

        $user->screens()->firstWhere('uuid', $request->uuid)->update($values);

        return response()->json([
            'error'  =>false,
            'message' => "Successfully updated Screen",
        ]);
    }

    public function getPlaylists(Request $request)
    {
        $user = Auth::user();
        $options = $user->playlists()->select('id','name','interval')->get();
		
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
            'Playlist' => $options,
        ]);

    }

    public function screens(Request $request)
    {
        $user = Auth::user();
        $options = $user->screens()->select('id','uuid','name','serial','lng','lat','brand','manufacturer','version','offline','os','sync_at')->get();

        return response()->json([
            'error'  =>false,
            'screens' => $options,
        ]);

    }

    public function viewPlaylist($id)
    {
        $user = Auth::user();

        $playlist = $user->playlists()->select('id','name','interval')->firstWhere('id', $id);
        $content = $playlist->playlistContent()->get();

        $playlistContent = [];
        $count = [ 'images' => 0, 'videos' => 0 ];

        foreach ($content as $item) {
           
            
        $item = $item->content()->first();

                if ($item->type == 'video') {
                    $playlistContent[] = [
                    	'id' => $item->id,
                    	'type' => $item->type,
                    	'name' => $item->name,
                        'type' => $item->type,
                         'mime' => $item->mime,
                         'file' => asset('storage/' .  $user->id . '/content/' . $item->name)
                    ];
                } else {
                    $playlistContent[] = [
                        'type' => $item->type, 'mime' => $item->mime,
                        'file' => asset('storage/' .  $user->id . '/content/' . $item->name)
                    ];
                }


            switch ($item->type) {
                case "image": $count['images']++; break;
                case "video": $count['videos']++; break;
            }
        }

        return response()->json([
            'error' => false,
            'user' => $user,
            'playlist' => $playlist,
            'content' => $playlistContent,
            'count' => $count
        ]);
    }

    public function updatePlaylist(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:225',
        ]);

        $user = Auth::user();

        $playlist = $user->playlists()->firstWhere('id', $request->id)->update([
            'name' => $request->name,
            'interval' => $request->interval ?? 10
        ]);

        return response()->json([
            'message'=> "You have successfully updated the playlist"]);
    }

    public function addPlaylist(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:225',
        ]);

        $user = Auth::user();

        $playlist = new \App\Playlist([
            'name' => $request->name,
            'interval' => $request->interval ?? 10
        ]);

        $user->playlists()->save($playlist);

        return response()->json([
            'message'=> "You have successfully added to the playlist"]);
    }

    public function delPlaylist($id)
    {

        $user = Auth::user();

        $playlist = $user->playlists()->firstWhere('id',$id);
        $playlist->delete();

       return response()->json([
            'message'=> "You have successfully deleted the playlist"]);;
    }

    public function delPlaylistContent($id,$cid)
    {

        $user = Auth::user();

        $playlist = $user->playlists()->firstWhere('id', $id);
        $item = \App\PlaylistContent::where('playlist_id', $playlist->id)->where('content_id', $cid)->first();

        if($item != null)
            $item->delete();


        return response()->json([
            'message'=> "You have successfully deleted the playlist content "]);;
    }

    public function setPlaylistContent(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);

        if ($request->hasFile('file')) {

            $user = Auth::user();
            $file = $request->file('file');

            //$allowedfileExtension=['pdf','jpg','png','docx'];

            $array ;

                $filename = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());

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

            if (is_numeric($request->playlist)) {
                $arrayPlaylist ;
                $playlist = $user->playlists()->select('id','name','interval')->firstWhere('id', $request->playlist);

                    if (!\App\PlaylistContent::where('content_id', $array->id)->where('playlist_id', $playlist->id)->exists()) {
                        $arrayPlaylist=  new \App\PlaylistContent([
                            'type' => $array->type,
                            'content_id' => $array->id,
                            'start_at' => 0,
                            'end_at' => 0
                        ]);

                }

                $playlist->playlistContent()->save($arrayPlaylist);
            }

            return response()->json([
                'message'=> 'Successfully added']);
        } else {
            return response()->json([
                'error'=>true,'message'=>'Unable to load images']);;
        }
    }

    public function addSchedule(Request $request)
    {
        $array = [];
        $user = Auth::user();

        $screen = $user->screens()->firstWhere('uuid', $request->uuid);
        $screen->schedules()->delete();

        if ($request->action === "update") {
            if ($request->fulltimePlaylist !== 'none') {
                $schedules = \App\Schedule::all();
                foreach($schedules as $schedule) {
                    $array[] = new \App\SchedulePlaylist([
                        'fulltime' => false,
                        'screen_id' => $screen->id,
                        'playlist_id' => $request->fulltimePlaylist,
                        'schedule_id' => $schedule->id,
                    ]);
                }
            }

            $screen->schedules()->saveMany($array);
        }

     return response()->json(['message' =>"Successfully Fulltime added"]);
    }

    public function addPartialSchedule(Request $request)
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
}
