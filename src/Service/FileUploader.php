<?php
namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $pictureDirectory;
    private $slugger;

    public function __construct($pictureDirectory, SluggerInterface $slugger)
    {
        $this->pictureDirectory = $pictureDirectory;
        $this->slugger = $slugger;
    }

    /**
     * Method to upload a picture
     *
     * @param UploadedFile $picture
     * @return void
     */
    public function uploadPicture(UploadedFile $picture)
    {
        $originalPicturename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
        $safePicturename = $this->slugger->slug($originalPicturename);
        $pictureName = $safePicturename.'-'.uniqid().'.'.$picture->guessExtension();

        try {
            $picture->move($this->getPictureDirectory(), $pictureName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $pictureName;
    }


    /**
     * Get the value of pictureDirectory
     */ 
    public function getPictureDirectory()
    {
        return $this->pictureDirectory;
    }
}