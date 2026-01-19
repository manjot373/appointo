<?php

namespace App\Services;

use App\Entity\BusinessUser;
use Doctrine\ORM\EntityManagerInterface;

class BusinessService
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function getBusinessByUser($user)
    {
        return $this->em->getRepository(BusinessUser::class)->findOneBy(['id' => $user->getId()]);
    }
}
