<?php

namespace App\Entity;

use App\Repository\AppointmentSlotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppointmentSlotRepository::class)]
class AppointmentSlot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $startTime;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $endTime;

    #[ORM\ManyToOne(inversedBy: 'appointmentSlots')]
    private ?Appointment $appointment = null;

    #[ORM\Column]
    private ?bool $is_booked = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): \DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): static
{
    $this->startTime = $startTime;
    return $this;
}

    public function getEndTime(): \DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeImmutable $endTime): static
{
    $this->endTime = $endTime;
    return $this;
}

    public function getAppointment(): ?Appointment
    {
        return $this->appointment;
    }

    public function setAppointment(?Appointment $appointment): static
    {
        $this->appointment = $appointment;

        return $this;
    }

    public function isBooked(): ?bool
    {
        return $this->is_booked;
    }

    public function setIsBooked(bool $is_booked): static
    {
        $this->is_booked = $is_booked;

        return $this;
    }

}
