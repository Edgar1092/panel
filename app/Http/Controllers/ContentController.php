<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Screen;
use App\User;
use App\Schedule;
use App\ScheduleUser;

// TEMPORAL FACADE
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    public function selectScreen()
    {
        $user = Auth::user();
        $screens = $user->screens()->orderBy('created_at', 'asc')->get();

        return view('content.screens', [
            'user' => $user,
            'screens' => $screens
        ]);
    }

    public function viewContent(Request $request)
    {
        if(!Auth::user()){
            return redirect('login');
        }
        $user = Auth::user();
        $count = [ 'images' => 0, 'videos' => 0 ];
        if($user->is_admin){
            $scrn = Screen::where('uuid', $request->uuid)->first();
            $userSelected= User::find($scrn->user_id);
            $screen = $userSelected->screens()->firstWhere('uuid', $request->uuid);
            $actives = $screen->schedules()->get();
            $content = $userSelected->contents()->get();
            $playlists = $userSelected->playlists()->get();
            $schedules = $userSelected->schedules()->get();
            $playlistsPromotores = User::where("is_promotor", 1)
            ->where("users.id","!=", $userSelected->id)
            ->join('playlists', 'playlists.user_id', '=', 'users.id')
            ->select('playlists.*')
            ->get();
            $schedules2 = Schedule::get();
        }else{
            $screen = $user->screens()->firstWhere('uuid', $request->uuid);  
            $actives = $screen->schedules()->get();
            $content = $user->contents()->get();
            $playlists = $user->playlists()->get();
            $schedules = $user->schedules()->get();
            $schedulesUserBlock = ScheduleUser::where('user_id', $user->id)->where('locked',1)->count();
        }

        /**
         * FIX WITHOUT BACKOFFICE ALL SCHEDULES
         */
        if($user->is_admin){
            if (count($schedules) < 1) {
                for ($i=1; $i <= 48; $i++) {
                    DB::table('schedule_users')->insert([
                        'schedule_id' => $i,
                        'user_id' => $userSelected->id,
                    ]);
                }
            }
        }else{
            if (count($schedules) < 1) {
                for ($i=1; $i <= 48; $i++) {
                    DB::table('schedule_users')->insert([
                        'schedule_id' => $i,
                        'user_id' => $user->id,
                    ]);
                }
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
        // print_r(count($schedules2));
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
        if($user->is_admin){
            $p =[];
            $playlists = $userSelected->playlists()->get();
        }else{
            $playlists = $user->playlists()->get();
        }

        // print_r($playlists);
        // echo '<br><br>';
        // print_r($playlistsPromotores);
        
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
        
        $playlitsFinal = [];
        if($user->is_admin){
            foreach ($playlists as $key => $value) {
                array_push($playlitsFinal,$value);
            }
            foreach ($playlistsPromotores as $key => $value) {
                array_push($playlitsFinal,$value);
            }
            // print_r(count($sectionSchedules));
        return view('admin.playlist.screen', [
            'user' => $user,
            'userSelected' => $userSelected,
            'screen' => $screen,
            'content' => $content,
            'playlists' => $playlists,
            'schedules' => $userSchedules,
            'sectionSchedules' => $sectionSchedules,
            'schedulesFull' =>$schedules2,
            'schedulesUsuario' => $schedules,
            'playlistsPromotores' => $playlistsPromotores,
            'actives' => $actives,
            'count' => $count,
            'playlitsFinal' => $playlitsFinal
        ]);
        }else{
            return view('playlist.screen', [
                'user' => $user,
                'screen' => $screen,
                'content' => $content,
                'playlists' => $playlists,
                'schedules' => $userSchedules,
                'sectionSchedules' => $sectionSchedules,
                'schedulesUsuario' => $schedules,
                'actives' => $actives,
                'count' => $count,
                'schedulesUserBlock'=>$schedulesUserBlock
            ]);
        }
    }

    public function userContent(Request $request)
    {
        $user = Auth::user();
        if($user->is_admin){
            $userSelected= User::find($request->uuid);
            $content = $userSelected->contents()->get();
            $playlist = $userSelected->playlists()->get();
            return view('admin.content.index', [
                'user' => $user,
                'userSelected' => $userSelected,
                'content' => $content,
                'playlist' => $playlist
            ]);
        }else{
            $content = $user->contents()->get();
            $playlist = $user->playlists()->get();
            return view('content.index', [
                'user' => $user,
                'content' => $content,
                'playlist' => $playlist
            ]);
        }
        

    }

    public function uploadContent(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);

        if ($request->hasFile('file')) {

            $userLog = Auth::user();
            
            if($userLog->is_admin){
                $user = User::find($request->idUser);
            }else{
                $user = Auth::user();
            }
            $files = $request->file('file');

            //$allowedfileExtension=['pdf','jpg','png','docx'];

            $array = [];

            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());

                //$check = in_array($extension, $allowedfileExtension);

                if (!\App\Content::where('name', '=', $filename)->where('user_id', $user->id)->exists()) {
                    $file->storeAs($user->id . '/content', $filename);

                    if (strpos($file->getMimeType(), 'video') !== false)
                          prepareVideo($user->id . '/content/' . $filename);

                    $array[] = new \App\Content([
                        'name' => $filename,
                        'type' => $extension,
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ]);
                } else {
                    $array[] = \App\Content::where('name', $filename)
                        ->where('type', $extension)
                        ->where('mime', $file->getMimeType())
                        ->where('size', $file->getSize())
                        ->first();
                }
            }

            //$content = new \App\Content($array);
            $user->contents()->saveMany($array);

            if (is_numeric($request->playlist)) {
                $arrayPlaylist = [];
                $playlist = $user->playlists()->firstWhere('id', $request->playlist);

                foreach ($array as $content) {
                    if (!\App\PlaylistContent::where('content_id', $content->id)->where('playlist_id', $playlist->id)->exists()) {
                        $arrayPlaylist[] =  new \App\PlaylistContent([
                            'type' => $content->type,
                            'content_id' => $content->id,
                            'start_at' => 0,
                            'end_at' => 0
                        ]);
                    }
                }
                
                $playlist->playlistContent()->saveMany($arrayPlaylist);
            }
            
            return back()->with('success', 'File uploaded!');
        } else {
            return back()->with('success', 'ERROR NO FILES!!!');
        }
    }

    public function delContent(Request $request)
    {
        $userLog = Auth::user();
            
        if($userLog->is_admin){
            $user = User::find($request->idUser);
        }else{
            $user = Auth::user();
        }
        $content = $request->content;

        foreach ($content as $index) {
            \App\PlaylistContent::where('content_id', $index)->delete();
            $user->contents()->where('id', $index)->delete();
        }

        return back()->with('success', 'Selected content was successfully removed!');
    }

    public function playlistContent(Request $request)
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
        
        $array = [];
        $content = $request->content;

        foreach ($content as $index) {
            $file = $user->contents()->firstWhere('id', $index);
            
            if ($playlist->playlistContent()->firstWhere('content_id', $file->id) === null) {
                $array[] =  new \App\PlaylistContent([
                    'type' => $file->type,
                    'content_id' => $file->id,
                    'start_at' => 0,
                    'end_at' => 0
                ]);
            }
        }
        
        if (!empty($array)) $playlist->playlistContent()->saveMany($array);

        return back()->with('success', 'Content attached to selected playlist!');
    }
}

