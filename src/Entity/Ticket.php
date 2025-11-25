<?php

namespace App\Entity;

use App\Enum\TicketCategoryEnum;
use App\Enum\TicketPriorityEnum;
use App\Enum\TicketStatusEnum;
use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(enumType: TicketStatusEnum::class)]
    private ?TicketStatusEnum $status = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(enumType: TicketPriorityEnum::class)]
    private ?TicketPriorityEnum $priority = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, TicketMessage>
     */
    #[ORM\OneToMany(targetEntity: TicketMessage::class, mappedBy: 'ticket')]
    private Collection $ticketMessages;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(enumType: TicketCategoryEnum::class)]
    private ?TicketCategoryEnum $category = null;

    #[ORM\PrePersist]
    public function setPrePersist(): void
    {
        if ($this->uuid === null) {
            $this->uuid = Uuid::v4()->jsonSerialize();
        }

        if ($this->createdAt === null && $this->updatedAt === null) {
            $dateTimeNow = new \DateTimeImmutable();
            $this->createdAt = $dateTimeNow;
            $this->updatedAt = $dateTimeNow;
        }
    }

    /**
     * Pre update variables
     */
    #[ORM\PreUpdate]
    public function setPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }


    public function __construct()
    {
        $this->ticketMessages = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getStatus(): ?TicketStatusEnum
    {
        return $this->status;
    }

    public function setStatus(TicketStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPriority(): ?TicketPriorityEnum
    {
        return $this->priority;
    }

    public function setPriority(TicketPriorityEnum $priority): static
    {
        $this->priority = $priority;

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

    /**
     * @return Collection<int, TicketMessage>
     */
    public function getTicketMessages(): Collection
    {
        return $this->ticketMessages;
    }

    public function addTicketMessage(TicketMessage $ticketMessage): static
    {
        if (!$this->ticketMessages->contains($ticketMessage)) {
            $this->ticketMessages->add($ticketMessage);
            $ticketMessage->setTicket($this);
        }

        return $this;
    }

    public function removeTicketMessage(TicketMessage $ticketMessage): static
    {
        if ($this->ticketMessages->removeElement($ticketMessage)) {
            // set the owning side to null (unless already changed)
            if ($ticketMessage->getTicket() === $this) {
                $ticketMessage->setTicket(null);
            }
        }

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

    public function getCategory(): ?TicketCategoryEnum
    {
        return $this->category;
    }

    public function setCategory(TicketCategoryEnum $category): static
    {
        $this->category = $category;

        return $this;
    }
}
