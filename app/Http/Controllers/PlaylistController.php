<?php

namespace App\Http\Controllers;

use Auth;
// use Screen;
use Illuminate\Http\Request;
use App\Screen;
use App\SchedulePlaylist;
use App\ScheduleUser;
use App\User;

class PlaylistController extends Controller
{

    public function getPlaylists(Request $request)
    {
        $user = Auth::user();
        if($user->is_admin){
            $userSelected = User::find($request->uuid);
            $options = $userSelected->playlists()->get();
            $screens = $userSelected->screens()->orderBy('created_at', 'asc')->get();

            return view('admin.playlist.list', [
                'user' => $user,
                'userSelected' => $userSelected,
                'list' => $options,
                'screens' => $screens,
            ]);
        }else{
            $options = $user->playlists()->get();
            $screens = $user->screens()->orderBy('created_at', 'asc')->get();

            return view('playlist.list', [
                'user' => $user,
                'list' => $options,
                'screens' => $screens,
            ]);   
        }
        
    }

    public function viewPlaylist(Request $request)
    {
        $user = Auth::user();
        if($user->is_admin){
            $userSelected = User::find($request->uuid);
            $playlist = $userSelected->playlists()->firstWhere('id', $request->id);
        }else{
            $playlist = $user->playlists()->firstWhere('id', $request->id);
        }
        
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
        if($user->is_admin){
            return view('admin.playlist.index', [
                'user' => $user,
                'userSelected'=>$userSelected,
                'playlist' => $playlist,
                'content' => $playlistContent,
                'count' => $count
            ]);
        }else{
            return view('playlist.index', [
                'user' => $user,
                'playlist' => $playlist,
                'content' => $playlistContent,
                'count' => $count
            ]);
        }
    }

    public function updatePlaylist(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:225',
        ]);

        $userLog = Auth::user();
            
        if($userLog->is_admin){
            $user = User::find($request->idUser);
        }else{
            $user = Auth::user();
        }

        $playlist = $user->playlists()->firstWhere('id', $request->id)->update([
            'name' => $request->name,
            'interval' => $request->interval ?? 10
        ]);

        return back()->with('success', "You have successfully updated the playlist");
    }

    public function addPlaylist(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:225',
        ]);

        $userLog = Auth::user();
            
        if($userLog->is_admin){
            $user = User::find($request->idUser);
        }else{
            $user = Auth::user();
        }

        $playlist = new \App\Playlist([
            'name' => $request->name,
            'interval' => $request->interval ?? 10
        ]);

        $user->playlists()->save($playlist);

        return back()->with('success', "You have successfully added the playlist: $request->name.");
    }

    public function delPlaylist(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $userLog = Auth::user();
            
        if($userLog->is_admin){
            $user = User::find($request->idUser);
        }else{
            $user = Auth::user();
        }

        $playlist = $user->playlists()->firstWhere('id', $request->id);
        $playlist->delete();

        return back()->with('success', "You have successfully removed the playlist: $playlist->name.")->with('color', 'danger');
    }

    public function delPlaylistContent(Request $request)
    {
        $request->validate([
            'playlist' => 'required'
        ]);

        $userLog = Auth::user();
            
        if($userLog->is_admin){
            $user = User::find($request->idUser);
        }else{
            $user = Auth::user();
        }

        $playlist = $user->playlists()->firstWhere('id', $request->id);        
        $content = $request->content;

        foreach ($content as $index) {
            $item = \App\PlaylistContent::where('playlist_id', $playlist->id)->where('content_id', $index)->first();
            $item->delete();
        }

        return back()->with('success', "Content removed from playlist $playlist->name!");
    }

    public function setPlaylistContent(Request $request)
    {
        $array = [];
        $user = Auth::user();

        if($user->is_admin){
            $scrn = Screen::where('uuid', $request->uuid)->first();
            $userSelected= User::find($scrn->user_id);
            $screen = $userSelected->screens()->firstWhere('uuid', $request->uuid);
            $screen->schedules()->delete();
        }else{
            $screen = $user->screens()->firstWhere('uuid', $request->uuid);
            $screen->schedules()->delete();  
        }
       

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
            } else {
                foreach($request->playlist as $key => $value) {
                    if ($value !== "none") {
                        $array[] = new \App\SchedulePlaylist([
                            'fulltime' => false,
                            'screen_id' => $screen->id,
                            'playlist_id' => $value,
                            'schedule_id' => $key,
                        ]);
                    }
                }
            }
            
            $screen->schedules()->saveMany($array);
        }

        return back()->with('success', 'Playlist successfully updated on this screen!');
    }

    public function lockShcedule(Request $request)
    {
        $array = [];
        $user = Auth::user();

       

        $scrn2 = ScheduleUser::where('user_id', $request->idUser)->get();

        foreach($scrn2 as $desbloqueo){
            $scrn3 = ScheduleUser::find($desbloqueo->id);

            $scrn3->locked=0;
            $scrn3->save(); 
        }

      foreach($request->schedule_id as $bloqueo){
        $scrn = ScheduleUser::where('user_id', $request->idUser)->where('schedule_id',$bloqueo)->first();
        $scrn->locked=1;
        $scrn->save();
      }


            // var_dump('pantalla',$scrn);
            // $userSelected= User::find($scrn->user_id);
            // $screen = SchedulePlaylist::where('screen_id',$scrn->id)->where('schedule_id',$request->id)->first();
            // $encontrar= ScheduleUser::find($screen->id);

    
       

        // if ($request->action === "update") {
        //     if ($request->fulltimePlaylist !== 'none') {
        //         $schedules = \App\Schedule::all();
        //         foreach($schedules as $schedule) {
        //             $array[] = new \App\SchedulePlaylist([
        //                 'fulltime' => false,
        //                 'screen_id' => $screen->id,
        //                 'playlist_id' => $request->fulltimePlaylist,
        //                 'schedule_id' => $schedule->id,
        //             ]);
        //         }
        //     } else {
        //         foreach($request->playlist as $key => $value) {
        //             if ($value !== "none") {
        //                 $array[] = new \App\SchedulePlaylist([
        //                     'fulltime' => false,
        //                     'screen_id' => $screen->id,
        //                     'playlist_id' => $value,
        //                     'schedule_id' => $key,
        //                 ]);
        //             }
        //         }
        //     }
            
        //     $screen->schedules()->saveMany($array);
        // }

         return back()->with('success', 'Schedule locked updated on this screen!');
    }
}
