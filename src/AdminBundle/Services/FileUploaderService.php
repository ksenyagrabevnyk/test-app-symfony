<?php

namespace AdminBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderService
{
    private $uploadDir;

    public function __construct($uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->uploadDir, $fileName);

        return $fileName;
    }

    public function getUploadDir()
    {
        return $this->uploadDir;
    }
}