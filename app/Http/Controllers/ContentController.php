<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Screen;
use App\User;

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
        }else{
            $screen = $user->screens()->firstWhere('uuid', $request->uuid);  
            $actives = $screen->schedules()->get();
            $content = $user->contents()->get();
            $playlists = $user->playlists()->get();
            $schedules = $user->schedules()->get();
        }

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
        if($user->is_admin){
            $playlists = $userSelected->playlists()->get();
        }else{
            $playlists = $user->playlists()->get();
        }
        
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
        if($user->is_admin){
        return view('admin.playlist.screen', [
            'user' => $user,
            'userSelected' => $userSelected,
            'screen' => $screen,
            'content' => $content,
            'playlists' => $playlists,
            'schedules' => $userSchedules,
            'sectionSchedules' => $sectionSchedules,
            'actives' => $actives,
            'count' => $count
        ]);
        }else{
            return view('playlist.screen', [
                'user' => $user,
                'screen' => $screen,
                'content' => $content,
                'playlists' => $playlists,
                'schedules' => $userSchedules,
                'sectionSchedules' => $sectionSchedules,
                'actives' => $actives,
                'count' => $count
            ]);
        }
    }

    public function userContent(Request $request)
    {
        $user = Auth::user();
        $content = $user->contents()->get();
        $playlist = $user->playlists()->get();

        return view('content.index', [
            'user' => $user,
            'content' => $content,
            'playlist' => $playlist
        ]);
    }

    public function uploadContent(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);

        if ($request->hasFile('file')) {

            $user = Auth::user();
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
        $user = Auth::user();
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

        $user = Auth::user();
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

