<?php

namespace App\Controller\admin;

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


#[Route('/business/user')]
final class BusinessUserController extends AbstractController
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
        $user = $this->getUser();
        $business = $this->bs->getBusinessByUser($user);
        $businessUser = $entityManager->getRepository(BusinessUser::class)->findBy(['business' => $business]);
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
        $user = $this->getUser();
        // $business = $this->bs->getBusinessByUser($user);
       
        $form = $this->createForm(BusinessUserType::class, $bu)
        ->add('roles', ChoiceType::class, [
                'choices' => ['ROLE_BUSINESS' => 'ROLE_BUSINESS', 'ROLE_STAFF' => 'ROLE_STAFF'],
                'placeholder' => 'Select a Role',
                'required' => false,
                'mapped' => false,
                'multiple' => true,
                'expanded' => true,
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->hashedPassword->hashPassword($bu, $bu->getPassword());
            $bu->setPassword($hashedPassword);
            $bu->setRoles($form->get('roles')->getData());
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
        if ($this->isCsrfTokenValid('delete'.$bu->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($bu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('business_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
