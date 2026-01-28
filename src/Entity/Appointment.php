<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BusinessUser $assignedUser = null;

    #[ORM\Column(length: 255)]
    private ?string $customerName = null;

    #[ORM\Column(length: 255)]
    private ?string $customerContact = null;


    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Business $business = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(length: 50)]
    private ?string $status = null;

    /**
     * @var Collection<int, AppointmentSlot>
     */
    #[ORM\OneToMany(targetEntity: AppointmentSlot::class, mappedBy: 'appointment')]
    private Collection $appointmentSlots;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    private ?Department $departmentt = null;

    public function __construct()
    {
        $this->appointmentSlots = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist(){
        $this->createdAt = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssignedUser(): ?BusinessUser
    {
        return $this->assignedUser;
    }

    public function setAssignedUser(?BusinessUser $assignedUser): static
    {
        $this->assignedUser = $assignedUser;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): static
    {
        $this->customerName = $customerName;

        return $this;
    }

    public function getCustomerContact(): ?string
    {
        return $this->customerContact;
    }

    public function setCustomerContact(string $customerContact): static
    {
        $this->customerContact = $customerContact;

        return $this;
    }

    public function getBusiness(): ?Business
    {
        return $this->business;
    }

    public function setBusiness(?Business $business): static
    {
        $this->business = $business;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
{
    $this->createdAt = $createdAt;
    return $this;
}

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, AppointmentSlot>
     */
    public function getAppointmentSlots(): Collection
    {
        return $this->appointmentSlots;
    }

    public function addAppointmentSlot(AppointmentSlot $appointmentSlot): static
    {
        if (!$this->appointmentSlots->contains($appointmentSlot)) {
            $this->appointmentSlots->add($appointmentSlot);
            $appointmentSlot->setAppointment($this);
        }

        return $this;
    }

    public function removeAppointmentSlot(AppointmentSlot $appointmentSlot): static
    {
        if ($this->appointmentSlots->removeElement($appointmentSlot)) {
            // set the owning side to null (unless already changed)
            if ($appointmentSlot->getAppointment() === $this) {
                $appointmentSlot->setAppointment(null);
            }
        }

        return $this;
    }

    public function getDepartmentt(): ?Department
    {
        return $this->departmentt;
    }

    public function setDepartmentt(?Department $departmentt): static
    {
        $this->departmentt = $departmentt;

        return $this;
    }
}
