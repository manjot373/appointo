<?php

namespace App\Controller\admin;

use App\Entity\Business;
use App\Entity\BusinessUser;
use App\Form\BusinessUserType;
use App\Repository\BusinessUserRepository;
use App\Services\BusinessService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/business/user')]
final class AdminBusinessUserController extends AbstractController
{
    private UserPasswordHasherInterface $hashedPassword;
    private BusinessService $bs;

    public function __construct(UserPasswordHasherInterface $hashedPassword, BusinessService $bs)
    {
    $this->hashedPassword = $hashedPassword;
    $this->bs = $bs;

    }
    #[Route('/', name: 'business_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $businessUser = $this->getUser();
        $businessUser = $entityManager->getRepository(BusinessUser::class)->findAll();
        return $this->render('admin/businessuser/index.html.twig', [
            'businessUser' => $businessUser
        ]);
    }

    
    // is granted ROLE_STAFF
    // #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'business_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bu = new BusinessUser();
        $business = $entityManager->getRepository(Business::class)->findAll();
       
        $form = $this->createForm(BusinessUserType::class, $bu)
        ->add('business', ChoiceType::class, [
            "choices" => $business,
            "placeholder" => "Select Business",
            "required" => true,
            "mapped" => false,
            "choice_label" => function ($business) {
                return $business->getName();
            }
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->hashedPassword->hashPassword($bu, $bu->getPassword());
            $bu->setBusiness($form->get('business')->getData());

            $bu->setPassword($hashedPassword);
            $bu->setRoles(["ROLE_BUSINESS", "ROLE_STAFF"]);
            // if(in_array('ROLE_STAFF', $bu->getRoles())){
            //     $bu->setBusiness($business);
            // }
            $entityManager->persist($bu);
            $entityManager->flush();

            return $this->redirectToRoute('business_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/businessuser/new.html.twig', [
            'businessUser' => $bu,
            'form' => $form,
        ]);
    }

    #[Route('/{guid}', name: 'business_user_show', methods: ['GET'])]
    public function show(BusinessUser $bu): Response
    {
        return $this->render('admin/businessuser/show.html.twig', [
            'businessUser' => $bu,
        ]);
    }

    #[Route('/{guid}/edit', name: 'business_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BusinessUser $bu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BusinessUserType::class, $bu);
        $form->handleRequest($request);
         $hashedPassword = $this->hashedPassword->hashPassword($bu, $bu->getPassword());
         $bu->setPassword($hashedPassword);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('business_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/businessuser/edit.html.twig', [
            'businessUser' => $bu,
            'form' => $form,
        ]);
    }

    #[Route('/{guid}', name: 'business_user_delete', methods: ['POST'])]
    public function delete(Request $request, BusinessUser $bu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bu->getGuid(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($bu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('business_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
