<?php

namespace App\Entity;

use App\Repository\BusinessRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BusinessRepository::class)]
class Business
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    /**
     * @var Collection<int, BusinessUser>
     */
    #[ORM\OneToMany(targetEntity: BusinessUser::class, mappedBy: 'business')]
    private Collection $businessUsers;

    #[ORM\ManyToOne(inversedBy: 'businesses')]
    private ?Category $category = null;

    /**
     * @var Collection<int, Department>
     */
    #[ORM\OneToMany(targetEntity: Department::class, mappedBy: 'business')]
    private Collection $departments;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\OneToMany(targetEntity: Appointment::class, mappedBy: 'business')]
    private Collection $appointments;

    public function __construct()
    {
        $this->businessUsers = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->appointments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, BusinessUser>
     */
    public function getBusinessUsers(): Collection
    {
        return $this->businessUsers;
    }

    public function addBusinessUser(BusinessUser $businessUser): static
    {
        if (!$this->businessUsers->contains($businessUser)) {
            $this->businessUsers->add($businessUser);
            $businessUser->setBusiness($this);
        }

        return $this;
    }

    public function removeBusinessUser(BusinessUser $businessUser): static
    {
        if ($this->businessUsers->removeElement($businessUser)) {
            // set the owning side to null (unless already changed)
            if ($businessUser->getBusiness() === $this) {
                $businessUser->setBusiness(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Department>
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): static
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->setBusiness($this);
        }

        return $this;
    }

    public function removeDepartment(Department $department): static
    {
        if ($this->departments->removeElement($department)) {
            // set the owning side to null (unless already changed)
            if ($department->getBusiness() === $this) {
                $department->setBusiness(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): static
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->setBusiness($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getBusiness() === $this) {
                $appointment->setBusiness(null);
            }
        }

        return $this;
    }

}
