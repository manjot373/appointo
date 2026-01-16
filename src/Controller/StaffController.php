<?php

namespace App\Controller;

use App\Entity\Staff;
use App\Form\StaffType;
use App\Repository\StaffRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/business/staff')]
final class StaffController extends AbstractController
{
    private UserPasswordHasherInterface $hashedPassword;

    public function __construct(UserPasswordHasherInterface $hashedPassword)
    {
    $this->hashedPassword = $hashedPassword;
    }
    #[Route(name: 'app_staff_index', methods: ['GET'])]
    public function index(StaffRepository $staffRepository): Response
    {
        $business = $this->getUser();
        return $this->render('business/staff/index.html.twig', [
            'staff' => $staffRepository->findBy(['business' => $business]),
        ]);
    }

    

    #[Route('/new', name: 'app_staff_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $staff = new Staff();
        $business = $this->getUser();
        $form = $this->createForm(StaffType::class, $staff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->hashedPassword->hashPassword($staff, $staff->getPassword());
            $staff->setPassword($hashedPassword);
            $staff->setBusiness($business);
            $entityManager->persist($staff);
            $entityManager->flush();

            return $this->redirectToRoute('app_staff_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('business/staff/new.html.twig', [
            'staff' => $staff,
            'form' => $form,
        ]);
    }

    #[Route('/{guid}', name: 'app_staff_show', methods: ['GET'])]
    public function show(Staff $staff): Response
    {
        return $this->render('business/staff/show.html.twig', [
            'staff' => $staff,
        ]);
    }

    #[Route('/{guid}/edit', name: 'app_staff_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Staff $staff, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StaffType::class, $staff);
        $form->handleRequest($request);
         $hashedPassword = $this->hashedPassword->hashPassword($staff, $staff->getPassword());
         $staff->setPassword($hashedPassword);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_staff_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('business/staff/edit.html.twig', [
            'staff' => $staff,
            'form' => $form,
        ]);
    }

    #[Route('/{guid}', name: 'app_staff_delete', methods: ['POST'])]
    public function delete(Request $request, Staff $staff, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$staff->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($staff);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_staff_index', [], Response::HTTP_SEE_OTHER);
    }
}
