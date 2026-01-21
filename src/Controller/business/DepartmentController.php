<?php

namespace App\Controller\business;

use App\Entity\Business;
use App\Entity\Department;
use App\Form\DepartmentType;
use App\Services\BusinessService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/business/department')]
final class DepartmentController extends AbstractController
{
    #[Route(name: 'app_department_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $businessUser = $this->getUser();
        $departments = $em->getRepository(Department::class)->findBy(["businessUser" => $businessUser]);
        return $this->render('business/department/index.html.twig', [
            'departments' => $departments
        ]);
    }

    #[Route('/new', name: 'app_department_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, BusinessService $bs ): Response
    {
        $department = new Department();
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);
        $businessUser = $this->getUser();
        $business = $bs->getBusinessByUser($businessUser);
        if ($form->isSubmitted() && $form->isValid()) {
            $department->setBusinessUser($businessUser);
            $department->setBusiness($business);
            $entityManager->persist($department);
            $entityManager->flush();

            return $this->redirectToRoute('app_department_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('business/department/new.html.twig', [
            'department' => $department,
            'form' => $form,
        ]);
    }

    #[Route('/{guid}', name: 'app_department_show', methods: ['GET'])]
    public function show(Department $department, EntityManagerInterface $em): Response
    {

        return $this->render('business/department/show.html.twig', [
            'department' => $department,
        ]);
    }

    #[Route('/{guid}/edit', name: 'app_department_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_department_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('business/department/edit.html.twig', [
            'department' => $department,
            'form' => $form,
        ]);
    }

    #[Route('/{guid}', name: 'app_department_delete', methods: ['POST'])]
    public function delete(Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $department->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($department);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_department_index', [], Response::HTTP_SEE_OTHER);
    }
}
