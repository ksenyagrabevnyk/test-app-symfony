<?php

namespace AdminBundle\Services;

use Doctrine\ORM\Mapping\Entity;
use Liip\ImagineBundle\Controller\ImagineController;
use Doctrine\ORM\EntityManager;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class SavePhotosService
{
    private $uploadDir;
    private $container;
    private $em;

    public function __construct($uploadDir, Container $container, EntityManager $em)
    {
        $this->uploadDir = $uploadDir;
        $this->container = $container;
        $this->em = $em;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function filterCroppedPhoto($entity)
    {
        /** @var CacheManager */
        $cacheManager = $this
            ->container
            ->get('liip_imagine.cache.manager');

        $path = $this->uploadDir . '/..' . $entity->getImgPath();
        $id = $entity->getId();
        $entityName = $this->em->getClassMetadata(get_class($entity))->getTableName();
        $savePath = $this->uploadDir . '/' . $entityName . '/' . $id . '/' . $id . '.jpg';

        if (file_exists($path)) {
            if(!file_exists($this->uploadDir . '/' . $entityName . '/' . $id)) {
                mkdir($this->uploadDir . '/' . $entityName . '/' . $id);
            }
            move_uploaded_file($path ,$savePath);
            unlink($path);
        }

        $newPath = 'uploads/' . $entityName . '/' . $id . '/' . $id . '.jpg';
        $filter = $this->chooseFilterByEntity($entityName, $savePath);
        $newFilterPath = 'uploads/' . $filter . '/uploads/' . $entityName . '/' . $id . '/' . $id . '.jpg';

        $cacheManager->resolve($newPath, $filter);
        $cacheManager->generateUrl($newPath, $filter);
        $cacheManager
            ->getBrowserPath(
                $newPath,
                $filter
            );
        $this->container->get('liip_imagine.controller')->filterAction(new Request(), $newPath, $filter);

        if ($entityName != 'phrases') {
            unlink($savePath);
            rmdir($this->uploadDir . '/' . $entityName . '/' . $id);
        }

        return $newFilterPath;
    }

    public function chooseFilterByEntity($entityName, $path)
    {
        /** @var $filter String */
        switch ($entityName) {
            case 'products':
                    $filter = 'product_list_down';
                break;
        }

        return $filter;
    }
}

