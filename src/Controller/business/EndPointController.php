<?php

namespace App\Controller\business;

use App\Entity\AppointmentSlot;
use App\Repository\AppointmentRepository;
use App\Repository\AppointmentSlotRepository;
use App\Services\BusinessService;
use App\Services\CommonService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/business/api')]
class EndPointController extends AbstractController
{
    #[Route('/slots', name: 'business_api_slots', methods: ['GET'])]
    public function getSlots(Request $request, AppointmentSlotRepository $repo, BusinessService $businessService): JsonResponse
    {
        $start = new \DateTimeImmutable($request->query->get('start'));
        $end   = (clone $start)->modify('+7 days');
        // $user = $this->getUser();
        // $business = $businessService->getBusinessByUser($user);

        $slots = $repo->createQueryBuilder('s')
            ->where('s.startTime BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();

        $events = [];

        foreach ($slots as $slot) {
            $events[] = [
                'title' => $slot->isBooked() ? 'Booked' : 'Available',
                'start' => $slot->getStartTime()->format(DATE_ATOM),
                'end'   => $slot->getEndTime()->format(DATE_ATOM),
                'color' => $slot->isBooked() ? '#e74c3c' : '#2ecc71'
            ];
        }

        return $this->json($events);
    }

    #[Route('/slots_create', methods: ['POST'])]
    public function createSlot(
        Request $request,
        EntityManagerInterface $em,
        AppointmentSlotRepository $repo,
        AppointmentRepository $appointmentRepo,
        CommonService $cs
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $start = $cs->myDate($data['startTime']);
        $end   = $cs->myDate($data['endTime']);
        $appointment = $appointmentRepo->find($data['appointmentId']);

        // Overlap check
        $overlap = $repo->createQueryBuilder('s')
            ->where('s.appointment = :appointment')
            ->andWhere('s.is_booked = :isBooked')
            ->andWhere(':start < s.endTime AND :end > s.startTime')
            ->setParameter('appointment', $appointment)
            ->setParameter('isBooked', true)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();

        if ($overlap) {
            return $this->json(['error' => 'Time slot overlaps existing slot']);
        }

        $slot = new AppointmentSlot();
        $slot->setStartTime($start);
        $slot->setEndTime($end);
        $slot->setAppointment($appointment);
        $slot->setIsBooked(true);   // new slots start unbooked

        $em->persist($slot);
        $em->flush();


        return $this->json(['status' => 'ok']);
    }
}
