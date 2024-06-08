<?php

namespace App\Services;

class ImageService
{
    public function uploadImage($file, $uploadPath): ?string
    {
        if ($file['error'] !== 0) {
            return null;
        }

        $allowedTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
        $filename = $file['name'];
        $filetype = $file['type'];
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if (!array_key_exists($extension, $allowedTypes) || !in_array($filetype, $allowedTypes)) {
            return null;
        }

        $filesize = $file['size'];
        if ($filesize > 1024 * 1024) {
            return null;
        }

        $newname = md5(uniqid());
        $newfilename = $uploadPath . $newname . '.' . $extension;

        if (move_uploaded_file($file['tmp_name'], $newfilename)) {
            return $newname . '.' . $extension;
        } else {
            return null;
        }
    }
    public function deleteImage($imagePath): void
    {
        if (file_exists($imagePath)){
            unlink($imagePath);
        }
    }

}