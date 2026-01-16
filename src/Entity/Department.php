<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'departments')]
    private ?Business $business = null;

    #[ORM\Column(type: Types::GUID, unique: true)]
    private ?string $guid = null;

    #[ORM\PrePersist]
    public function generateGuid(): void
    {
        if ($this->guid === null) {
            $this->guid = Uuid::v7();
        }
    }

    // Getters / setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
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

    public function getBusiness(): ?Business
    {
        return $this->business;
    }

    public function setBusiness(?Business $business): static
    {
        $this->business = $business;
        return $this;
    }
}
