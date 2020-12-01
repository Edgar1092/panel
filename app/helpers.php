<?php

use Illuminate\Support\Facades\Storage;
//use Symfony\Component\Process\Process;

//D:\Development\ffmpeg-4.3.1-win64-static\bin\ffmpeg.exe
//D:\Development\ffmpeg-4.3.1-win64-static\bin\ffprobe.exe

function getFFPath() {
    return [
        'ffmpeg' => '/usr/bin/ffmpeg',
        'ffprobe' => '/usr/bin/ffprobe'
    ];
}

function getVideoPreview($file)
{
    $fftool = getFFPath();

    $path = storage_path() . '/app/public/' . $file;
    $prev = '.png';

    $filePreview = $path . $prev;

    if (file_exists($filePreview)) {
        return asset('storage/' . $file . $prev);
    } else {
        try {
            $ffmpeg = FFMpeg\FFMpeg::create(array(
                'ffmpeg.binaries'  => $fftool['ffmpeg'], 
                'ffprobe.binaries' => $fftool['ffprobe'],
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12,
            ), \Log::getLogger());

            $video = $ffmpeg->open($path);

            $frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(3));
            $frame->save($filePreview);

            return asset('storage/' . $file . $prev);
        } catch (Exception $e) {
            return "https://www.flaticon.com/svg/static/icons/svg/1179/1179120.svg";
        }
    }
}

function prepareVideo($file)
{
    $fftool = getFFPath();

    $path = storage_path() . '/app/public/' . $file;

    $filePreview = $path . '.png';
    $fileFormatted = $path . '.webm';
    $fileCompressed = $path . '.x264.mp4';
    
    /*if (file_exists($path)) {
        $extension_pos = strrpos($path, '.');
        $path = substr($path, 0, $extension_pos) . '_' . date("YmdHis") .'_'. substr($path, $extension_pos);
    }*/

    try {
        $ffmpeg = FFMpeg\FFMpeg::create(array(
            'ffmpeg.binaries'  => $fftool['ffmpeg'], 
            'ffprobe.binaries' => $fftool['ffprobe'],
            'timeout'          => 3600,
            'ffmpeg.threads'   => 12,
        ));

        $video = $ffmpeg->open($path);

        if (!file_exists($filePreview)) {
            $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(3))
                  ->save($filePreview);
        }

        /*if (!file_exists($fileFormatted)) {
            $video->save(new FFMpeg\Format\Video\WebM(), $fileFormatted);
        }*/

        if (!file_exists($fileCompressed)) {
            /* $dimension = new FFMpeg\Coordinate\Dimension(1280, 720);
            $video->filters()
                  ->resize($dimension, FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_FIT, true)
                  ->synchronize();

            $video->save(new FFMpeg\Format\Video\X264(), $fileCompressed); */
            //exec($fftool['ffmpeg'] . " -i '" . $path . "' -vcodec libx264 -bt 100k -speed 4 -c:a aac -ac 2 -s 1280x720 '". $fileCompressed ."'");
            exec($fftool['ffmpeg'] . " -i \"" . $path . "\" -c:v libx264 -preset slow -crf 24 -s 1280x720 -c:a aac -b:a 128K \"". $fileCompressed ."\" 2>&1");
            //$process = Process::fromShellCommandline($fftool['ffmpeg'] . " -i \"" . $path . "\" -c:v libx264 -preset slow -crf 22 -s 1280x720 -c:a aac -b:a 128K \"". $fileCompressed ."\"");
        }
    } catch (Exception $e) {return $e;}
}
