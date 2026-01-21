<?php

namespace App\Controller\admin;

use App\Entity\Business;
use App\Form\BusinessType;
use App\Repository\BusinessRepository;
use App\Services\BusinessService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/business')]
final class BusinessController extends AbstractController
{
    #[Route(name: 'app_business_index', methods: ['GET'])]
    public function index(BusinessRepository $businessRepository, BusinessService $bs): Response
    {
       
        return $this->render('admin/business/index.html.twig', [
            'businesses' => $businessRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_business_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $business = new Business();
        $form = $this->createForm(BusinessType::class, $business);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($business);
            $entityManager->flush();

            $lastBusiness = $entityManager->getRepository(Business::class)->findLastOne();

            return $this->redirectToRoute('app_business_show', ['id' => $lastBusiness->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/business/new.html.twig', [
            'business' => $business,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_business_show', methods: ['GET'])]
    public function show(Business $business): Response
    {
        return $this->render('admin/business/show.html.twig', [
            'business' => $business,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_business_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Business $business, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BusinessType::class, $business);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_business_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/business/edit.html.twig', [
            'business' => $business,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_business_delete', methods: ['POST'])]
    public function delete(Request $request, Business $business, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$business->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($business);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_business_index', [], Response::HTTP_SEE_OTHER);
    }
}
