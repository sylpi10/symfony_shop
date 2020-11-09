<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;

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

    public function uploadPic($event)
    {
        $entity = $event->getEntityInstance();

        $tmp_name = $_FILES['Product']['tmp_name']['picture'];
        $filename = uniqid();
        $extension = pathinfo($_FILES['Product']['name']['picture'], PATHINFO_EXTENSION);

        $project_dir = $this->appKernel->getProjectDir();

        move_uploaded_file($tmp_name, $project_dir.'/public/uploads/'.$filename.'.'.$extension);
        
        $entity->setPicture($filename.'.'.$extension);
    }

    public function updatePicture(BeforeEntityUpdatedEvent $event)
    {
        if (!$event ->getEntityInstance() instanceof Product) {
            return;
        }

        if ($_FILES['Product']['tmp_name']['picture'] != '') {
            $this->uploadPic($event);
        }
    }

    public function setPicture(BeforeEntityPersistedEvent $event)
    {
        if (!$event ->getEntityInstance() instanceof Product) {
            return;
        }
        
       $this->uploadPic($event);
    }

}