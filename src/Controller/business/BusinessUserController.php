<?php

namespace App\Controller\business;

use App\Entity\BusinessUser;
use App\Services\BusinessService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/business/staff')]

final class BusinessUserController extends AbstractController{
    

private UserPasswordHasherInterface $passwordHasher;

public function __construct(UserPasswordHasherInterface $passwordHasher){
    $this->passwordHasher = $passwordHasher;
}

#[Route('/', name:'user_index') ]
public function user_index(EntityManagerInterface $em, BusinessService $bs){

    $user = $this->getUser();
    $business = $bs->getBusinessByUser($user);

    $businessUsers = $em->getRepository(BusinessUser::class)->findBy(['business' => $business]);    

    return $this->render('business/businessuser/index.html.twig', ['businessUsers' => $businessUsers]);
}

#[Route('/new', name:'new_user')]
public function new_user(EntityManagerInterface $em, BusinessService $bs, Request $request){
    $user = $this->getUser();
    $business = $bs->getBusinessByUser($user);

    $form = $this->createFormBuilder()
    ->add('username', TextType::class)
    ->add('email', EmailType::class)
    ->add('password', PasswordType::class)
    ->getForm();
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){

    $businessUser = new BusinessUser();
    $businessUser->setUsername($form->get('username')->getData())
    ->setEmail($form->get('email')->getData())
    ->setPassword($form->get('password')->getData())
    ->setBusiness($business)
    ->setRoles(['ROLE_STAFF']);
    $hashedPassword = $this->passwordHasher->hashPassword($businessUser, $businessUser->getPassword());
    $businessUser->setPassword($hashedPassword);
    $em->persist($businessUser);
    $em->flush();

    return $this->redirectToRoute('user_index');
    }

    return $this->render('business/businessuser/create.html.twig', ['form' => $form->createView()]);
}

#[Route('/{guid}/edit', name:'edit_user')]
public function edit_user(EntityManagerInterface $em, BusinessService $bs, Request $request, $guid){

    $user = $this->getUser();
    $business = $bs->getBusinessByUser($user);
    $businessUser = $em->getRepository(BusinessUser::class)->findOneBy(['guid' => $guid]);

    $form = $this->createFormBuilder($businessUser)
    ->add('username', TextType::class)
    ->add('email', EmailType::class)
    ->add('password', PasswordType::class)
    ->getForm();
    $form->handleRequest($request);


    if($form->isSubmitted() && $form->isValid()){

    $businessUser->setUsername($form->get('username')->getData());
    $businessUser->setEmail($form->get('email')->getData());
    $businessUser->setPassword($form->get('password')->getData());
    $hashedPassword = $this->passwordHasher->hashPassword($businessUser, $businessUser->getPassword());
    $businessUser->setPassword($hashedPassword);

    $em->persist($businessUser);
    $em->flush();

    return $this->redirectToRoute('user_index');

}

return $this->render('business/businessuser/edit.html.twig', ['businessUser' => $businessUser, 'form' => $form]);
}



#[Route('/{guid}/delete', name:'delete_user', methods:['POST'])]
public function delete_user(EntityManagerInterface $em, BusinessService $bs, $guid, Request $request){
    $user = $this->getUser();
    $businessUser = $em->getRepository(BusinessUser::class)->findOneBy(['guid' => $guid]);
    

    if ($this->isCsrfTokenValid('delete'.$businessUser->getGuid(), $request->getPayload()->getString('_token'))) {
            $em->remove($businessUser);
            $em->flush();

            }
            return $this->redirectToRoute('user_index',[], Response::HTTP_SEE_OTHER);

    }
}
