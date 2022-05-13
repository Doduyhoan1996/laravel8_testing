<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImageHelper
{
    public static function saveImageStorage($file_image, $folder) {
        if (empty($file_image)) {
            return ;
        }

        $extension = $file_image->getClientOriginalExtension();
        $file_name = explode('.', $file_image->getClientOriginalName())[0];
        $file_name = sprintf(date('YmdHis') . '_' . $file_name . '.%s', $extension);
        $storage = Storage::disk('public');
        $check_directory = $storage->exists($folder);
        if (!$check_directory) {
            $storage->makeDirectory($folder);
        }

        $storage->put($folder . '/' .$file_name,  File::get($file_image));
        return $file_name;
    }

    public static function removeImage($image_remove, $folder){
        try {
            if (empty($image_remove)) {
                return;
            }
            $storage = Storage::disk('public');
            $image_path = $folder .'/'. $image_remove;
            if ($storage->exists($image_path)) {
                $storage->delete($image_path);
            }
            return;
        } catch (\Exception $e) {
            Log::info('---Remove Image Storage---');
            Log::error($e->getMessage());
        }
    }

    public static function getImageInfo($image, $folder){
        $image_info = [
            'name' => $image
        ];
        if (empty($image)) {
            return $image_info;
        }
        $storage = Storage::disk('public');
        $image_path = $folder .'/'. $image;
        if ($storage->exists($image_path)) {
            $image_info['size'] = $storage->size($image_path);
            $image_info['path'] =  asset('storage/' . $image_path);
        }
        return $image_info;
    }

    public static function getImage($image, $folder){
        if (empty($image)) {
            return ;
        }
        $storage = Storage::disk('public');
        $image_path = $folder .'/'. $image;
        $image_url = '';
        if ($storage->exists($image_path)) {
            $image_url =  asset('storage/' . $image_path);
        }
        return $image_url;
    }

}
