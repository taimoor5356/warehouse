<?php

namespace App\Traits;
use AWS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait ImageUpload{
    public function uploadImage($file, $filename, $directory)
    {
        // $store = Storage::disk('s3')->put($photo_file_path, file_get_contents($file));

        if (isset($file)) {
            if ($file->isValid()) {
                $s3 = Storage::disk('s3');
                $s3->put("$directory$filename", fopen($file->getPathname(), 'r'), 'public');
            }
        }
    }
}