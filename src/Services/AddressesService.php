<?php

namespace App\Services;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddressesService
{
    private AddressRepository $addressRepo;
    private EntityManagerInterface $manager;

    public function __construct(AddressRepository $addressRepo,EntityManagerInterface $manager)
    {
        $this->addressRepo = $addressRepo;
        $this->manager = $manager;
    }

    
    public function delete(Address $address)
    {
        $address = $this->addressRepo->find($address);
        if ($address) {
            $this->manager->remove($address);
            $this->manager->flush();
        }
    }
}