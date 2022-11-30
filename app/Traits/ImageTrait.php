<?php

namespace App\Traits;

use Image;

trait ImageTrait {

    public function resizeImage($image, $width, $height, $path, $quality, $watermark = null, $color = '#ffffff')
    {
        $frame = Image::canvas($width, $height, $color);
        $newImage = Image::make($image);

        if ($newImage->width() <= $newImage->height() AND $newImage->height() >= $height) {
            $newImage->resize(null, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        elseif ($newImage->width() >= $width) {
            $newImage->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        if ($newImage->width() > $width OR $newImage->height() > $height) {
            $newImage->crop($width, $height);
        }

        $frame->insert($newImage, 'center');

        if ($watermark != null) {
            $frame->insert($watermark, 'bottom-left', 65, 65);
        }

        $frame->save(public_path().$path, $quality);
    }

    public function resizeOptimalImage($image, $width, $height, $path, $quality, $watermark = null, $color = '#ffffff')
    {
        $frame = Image::canvas($width, $height, $color);
        $newImage = Image::make($image);

        if ($newImage->width() > $newImage->height()) {
            $newImage->resize(null, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        elseif ($newImage->width() < $newImage->height()) {
            $newImage->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        // if ($width > $height) {
        //     $newImage->resize(null, $height, function ($constraint) {
        //         $constraint->aspectRatio();
        //     });
        // }
        // else {
        //     $newImage->resize($width, null, function ($constraint) {
        //         $constraint->aspectRatio();
        //     });
        // }

        $newImage->crop($width, $height);
        $newImage->rotate(0);

        $frame->insert($newImage, 'center');

        if ($watermark != null) {
            $frame->insert($watermark, 'bottom-left', 65, 65);
        }

        $frame->save(public_path().$path, $quality);
    }

    public function cropImage($image, $width, $height, $path, $quality)
    {
        $newImage = Image::make($image);

        if ($newImage->width() > $width OR $newImage->height() > $height) {
            $newImage->crop($width, $height);
        }

        $newImage->save(public_path().$path, $quality);
    }
}