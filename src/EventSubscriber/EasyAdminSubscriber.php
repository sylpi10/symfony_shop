<?php

namespace App\EventSubscriber;

use ReflectionClass;
use App\Entity\Slider;
use App\Entity\Product;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $appKernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setPicture'], 
            BeforeEntityUpdatedEvent::class => ['updatePicture']
        ];
    }

    public function uploadPic($event, $entityName)
    {
        $entity = $event->getEntityInstance();

        $tmp_name = $_FILES[$entityName]['tmp_name']['picture'];
        $filename = uniqid();
        $extension = pathinfo($_FILES[$entityName]['name']['picture'], PATHINFO_EXTENSION);

        $project_dir = $this->appKernel->getProjectDir();

        move_uploaded_file($tmp_name, $project_dir.'/public/uploads/'.$filename.'.'.$extension);
        
        $entity->setPicture($filename.'.'.$extension);
    }

    public function updatePicture(BeforeEntityUpdatedEvent $event)
    {
        if (!($event ->getEntityInstance() instanceof Product) && !($event ->getEntityInstance() instanceof Slider)) {
            return;
        }

        $reflexion = new \ReflectionClass($event ->getEntityInstance());
        $entityName = $reflexion->getShortName();

        if ($_FILES[$entityName]['tmp_name']['picture'] != '') {
            $this->uploadPic($event, $entityName);
        }
    }

    public function setPicture(BeforeEntityPersistedEvent $event)
    {

        $reflexion = new \ReflectionClass($event ->getEntityInstance());
        $entityName = $reflexion->getShortName();

        if (!($event ->getEntityInstance() instanceof Product) && !($event ->getEntityInstance() instanceof Slider)) {
            return;
        }
        
       $this->uploadPic($event, $entityName);
    }

}