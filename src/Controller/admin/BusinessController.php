<?php

namespace App\Controller\admin;

use App\Entity\Business;
use App\Entity\Category;
use App\Form\BusinessType;
use App\Repository\BusinessRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/business')]
final class BusinessController extends AbstractController
{
    
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ){
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }


    #[Route(name: 'app_business_index', methods: ['GET'])]
    public function index(BusinessRepository $businessRepository): Response
    {
        return $this->render('admin/business/index.html.twig', [
            'businesses' => $businessRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_business_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $business = new Business();
        $form = $this->createFormBuilder($business)
        ->add('name')
        ->add('password')
        ->add('address')
        ->add('email')
        ->add('category', ChoiceType::class, [
            'choices' => $this->getCategories(),
            'placeholder' => 'Select a parent category',
            'required' => false,
            'mapped' => false,
        ])
        
        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cid = $form->get('category')->getData();
            $category = $this->entityManager->getRepository(Category::class)->find($cid);
            if($category){
                $business->setCategory($category);
            }
            $hashedPassword = $this->passwordHasher->hashPassword($business, $business->getPassword()
        );

        $business->setPassword($hashedPassword);

            // $business->setCategory($category);
            $entityManager->persist($business);
            $entityManager->flush();

            return $this->redirectToRoute('app_business_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/business/new.html.twig', [
            'business' => $business,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_business_show', methods: ['GET'])]
    public function show(Business $business): Response
    {
        return $this->render('admin/business/show.html.twig', [
            'business' => $business,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_business_edit', methods: ['GET', 'POST'])]
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

    #[Route('/delete/{id}', name: 'app_business_delete', methods: ['POST'])]
    public function delete(Request $request, Business $business, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$business->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($business);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_business_index', [], Response::HTTP_SEE_OTHER);
    }

    public function getCategories(){
        $categories = $this->entityManager->getRepository(Category::class)->findBy(['parent' => null]);
        $array = [];
        foreach($categories as $category){

            $array[$category->getName()] = $category->getId();


            $subcategories = $this->getSubCategories($category->getId());
            if($subcategories){
                $array = array_merge($array, $subcategories);
        }
    }
    return $array;
    }
    public function getSubCategories($cid){
            $subcategories = $this->entityManager->getRepository(Category::class)->findBy(['parent' => $cid]);
            $array = [];
            foreach($subcategories as $subcategory){
                $array[" - ". $subcategory->getName()] = $subcategory->getId();
            }
            return $array;
        }
}
