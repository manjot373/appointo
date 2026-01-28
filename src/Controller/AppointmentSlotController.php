<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\AppointmentSlot;
use App\Entity\Department;
use App\Form\AppointmentSlotType;
use App\Repository\AppointmentSlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/business/appointment/slot')]
final class AppointmentSlotController extends AbstractController
{
    #[Route(name: 'app_appointment_slot_index', methods: ['GET'])]
    public function index(AppointmentSlotRepository $appointmentSlotRepository): Response
    {
        return $this->render('business/appointment_slot/index.html.twig', [
            'appointment_slots' => $appointmentSlotRepository->findAll(),
            
        ]);
    }

    #[Route('/new/{id}', name: 'app_appointment_slot_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $appointmentId = $request->attributes->get('id');
        $department = $entityManager->getRepository(Department::class)->find($appointmentId);
        $appointmentSlot = new AppointmentSlot();
        $form = $this->createForm(AppointmentSlotType::class, $appointmentSlot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointmentSlot->setAppointment($entityManager->getRepository(Appointment::class)->find($appointmentId));
            $entityManager->persist($appointmentSlot);
            $entityManager->flush();

            return $this->redirectToRoute('app_appointment_slot_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('business/appointment_slot/new.html.twig', [
            'appointmentId' => $appointmentId,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_appointment_slot_show', methods: ['GET'])]
    public function show(AppointmentSlot $appointmentSlot): Response
    {
        return $this->render('business/appointment_slot/show.html.twig', [
            'appointment_slot' => $appointmentSlot,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_appointment_slot_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AppointmentSlot $appointmentSlot, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AppointmentSlotType::class, $appointmentSlot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_appointment_slot_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('business/appointment_slot/edit.html.twig', [
            'appointment_slot' => $appointmentSlot,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_appointment_slot_delete', methods: ['POST'])]
    public function delete(Request $request, AppointmentSlot $appointmentSlot, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appointmentSlot->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($appointmentSlot);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_appointment_slot_index', [], Response::HTTP_SEE_OTHER);
    }
}
