<?php

namespace App\Entity;

use App\Enum\MaintenanceStatusEnum;
use App\Enum\MaintenanceTypeEnum;
use App\Repository\MaintenanceRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MaintenanceRepository::class)]
class Maintenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'maintenances')]
    private ?Item $item = null;

    #[ORM\Column(enumType: MaintenanceTypeEnum::class)]
    private ?MaintenanceTypeEnum $maintenanceType = null;

    #[ORM\Column(enumType: MaintenanceStatusEnum::class)]
    private ?MaintenanceStatusEnum $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $scheduledAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $nextMaintenanceAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $performedByName = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $performedByUser = null;

    #[ORM\Column(nullable: true)]
    private ?float $cost = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    /**
     * Pre persist variables
     */
    #[ORM\PrePersist]
    public function generateUuid(): void
    {
        if ($this->uuid === null) {
            $this->uuid = Uuid::v4();
        }
    }

    #[ORM\PrePersist]
    public function setDateTimeCreateAndupdateAtInit(): void
    {
        if($this->createdAt === null && $this->updatedAt === null) {
            $dateTimeNow = new DateTimeImmutable();
            $this->createdAt = $dateTimeNow;
            $this->updatedAt = $dateTimeNow;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getMaintenanceType(): ?MaintenanceTypeEnum
    {
        return $this->maintenanceType;
    }

    public function setMaintenanceType(MaintenanceTypeEnum $maintenanceType): static
    {
        $this->maintenanceType = $maintenanceType;

        return $this;
    }

    public function getStatus(): ?MaintenanceStatusEnum
    {
        return $this->status;
    }

    public function setStatus(MaintenanceStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getScheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(?\DateTimeImmutable $scheduledAt): static
    {
        $this->scheduledAt = $scheduledAt;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getPerformedByName(): ?string
    {
        return $this->performedByName;
    }

    public function setPerformedByName(?string $performedByName): static
    {
        $this->performedByName = $performedByName;

        return $this;
    }

    public function getPerformedByUser(): ?User
    {
        return $this->performedByUser;
    }

    public function setPerformedByUser(?User $performedByUser): static
    {
        $this->performedByUser = $performedByUser;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(?float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getNextMaintenanceAt(): ?\DateTimeImmutable
    {
        return $this->nextMaintenanceAt;
    }

    public function setNextMaintenanceAt(\DateTimeImmutable $nextMaintenanceAt): static
    {
        $this->nextMaintenanceAt = $nextMaintenanceAt;

        return $this;
    }
}
