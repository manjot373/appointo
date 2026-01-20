<?php
namespace App\Twig\Runtime;

use App\Entity\Business;
use App\Services\BusinessService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    private Security $security;
    private EntityManagerInterface $em;
    private BusinessService $businessService;

    public function __construct(Security $security, EntityManagerInterface $em, BusinessService $businessService)
    {
        $this->security = $security;
        $this->em = $em;
        $this->businessService = $businessService;
    }

    public function getBusiness()
    {
        $user = $this->security->getUser();
        if (!$user) {
            return null;
        }
        $business = $this->businessService->getBusinessByUser($user);
        return $business;
    }
}
