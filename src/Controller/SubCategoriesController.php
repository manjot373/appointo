<?php

namespace App\Controller;

use App\Entity\SubCategories;
use App\Form\SubCategoriesType;
use App\Repository\SubCategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/sub-categories')]
final class SubCategoriesController extends AbstractController
{
    #[Route(name: 'app_sub_categories_index', methods: ['GET'])]
    public function index(SubCategoriesRepository $subCategoriesRepository): Response
    {
        return $this->render('admin/sub_categories/index.html.twig', [
            'sub_categories' => $subCategoriesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sub_categories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subCategory = new SubCategories();
        $form = $this->createForm(SubCategoriesType::class, $subCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subCategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_sub_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/sub_categories/new.html.twig', [
            'sub_category' => $subCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sub_categories_show', methods: ['GET'])]
    public function show(SubCategories $subCategory): Response
    {
        return $this->render('admin/sub_categories/show.html.twig', [
            'sub_category' => $subCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sub_categories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubCategories $subCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubCategoriesType::class, $subCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sub_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/sub_categories/edit.html.twig', [
            'sub_category' => $subCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sub_categories_delete', methods: ['POST'])]
    public function delete(Request $request, SubCategories $subCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subCategory->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($subCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sub_categories_index', [], Response::HTTP_SEE_OTHER);
    }
}
