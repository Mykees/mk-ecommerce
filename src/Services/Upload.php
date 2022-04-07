<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Upload
{

    public function uploadImage (UploadedFile $uploadedFile, SluggerInterface $slugger, string $dest): string
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $uploadedFile->move(
                $dest,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            echo 'File Exception : ',  $e->getMessage(), "\n";
        }

        return $newFilename;
    }

}