<?php

namespace AdminBundle\Helper;

use AdminBundle\Entity\Products;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 */
class UploadImageHelper
{
    private $temp;

    /**
     * @var string
     *
     * @Assert\File(maxSize="5M")
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    protected $photoName;

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="path_name", type="string", length=255, nullable=true)
//     */
//    protected $pathName;
    
    /**
     * Set photo
     *
     * @param UploadedFile $photo
     * @return Theme
     */
    public function setPhoto(UploadedFile $photo = null)
    {
        $this->photo = $photo;
        if (isset($this->photoName)) {
            // store the old name to delete after the update
            $this->temp = $this->photoName;
            $this->photoName = null;
        } else {
            $this->photoName = 'initial';
        }
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhotoName()
    {
        return $this->photoName;
    }
    
//    /**
//     * Get pathName
//     *
//     * @return string
//     */
//    public function getPathName()
//    {
//        $dir = $this->container->getParameter('kernel.root_dir') . '/../web/uploads/phrases/'. $entity->getId();
//
//        return $this->pathName;
//    }
//    
//    if(!empty($uploadedFiles)) {
//        $fileName = $uploadedFiles->getClientOriginalName();
//        $dir = $this->container->getParameter('kernel.root_dir') . '/../web/uploads/phrases/'. $entity->getId();
//        //                $dir = '/../web/uploads/phrases/'. $entity->getId();
//        //                var_dump($dir); die;
//        $audioPath = $dir .  '/'. $fileName;
//        $uploadedFiles->move($dir, $fileName);
//        $entity->setAudio($fileName);
//        $entity->setAudioPath($audioPath);
//        $em->persist($entity);
//        $em->flush();
//    }



    public function getAbsolutePath()
    {
        return null === $this->photoName
            ? null
            : $this->getUploadRootDir().'/'.$this->photoName;
    }

    public function getWebPath()
    {
        return null === $this->photoName
            ? null
            : $this->getUploadDir().'/'.$this->photoName;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/' . $this->getEntityName() . '/' . $this->getId();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getPhoto()) {
            // do whatever you want to generate a unique name
            $filename = uniqid();
            $this->photoName = $filename.'.'.$this->getPhoto()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getPhoto()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getPhoto()->move($this->getUploadRootDir(), $this->photoName);

        // check if we have an old image
        if (isset($this->temp)) {

            if(file_exists($this->getUploadRootDir() . '/' . $this->temp)) {
                // delete the old image
                unlink($this->getUploadRootDir() . '/' . $this->temp);
            }
            // clear the temp image path
            $this->temp = null;
        }
        $this->photo = null;
    }

    /**
     * @ORM\PreRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
            $this->photoName = null;

            return true;
        }
    }

    public function getEntityName()
    {
        return strtolower(substr(strrchr(get_class($this), "\\"), 1));
    }

    public function getFixturesPhotoPath()
    {
        return __DIR__ . '/../../../web/includes/images/fixtures/' . $this->getEntityName() . '/';
    }

    public function getCopyImagePath($imageName)
    {
        copy($this->getFixturesPhotoPath() . $imageName, $this->getFixturesPhotoPath() . 'copy-' . $imageName);

        return $this->getFixturesPhotoPath() . 'copy-' . $imageName;
    }
}
