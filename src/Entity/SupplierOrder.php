<?php

namespace App\Entity;

use App\Repository\SupplierOrderRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SupplierOrderRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SupplierOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'supplierOrders')]
    private ?Supplier $supplier = null;

    #[ORM\ManyToOne(inversedBy: 'supplierOrders')]
    private ?Company $company = null;

    #[ORM\ManyToOne(inversedBy: 'supplierOrders')]
    private ?Currency $currency = null;

    #[ORM\Column]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $orderDate = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $expectedDeliveryDate = null;

    #[ORM\Column(type:Types::DECIMAL)]
    private ?float $totalAmount = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

    #[ORM\Column]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $deletedAt = null;

    /**
     * @var Collection<int, SupplierOrderItem>
     */
    #[ORM\OneToMany(targetEntity: SupplierOrderItem::class, mappedBy: 'supplierOrder')]
    private Collection $supplierOrderItems;

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

    public function __construct()
    {
        $this->supplierOrderItems = new ArrayCollection();
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

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): static
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeImmutable
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeImmutable $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getExpectedDeliveryDate(): ?\DateTimeImmutable
    {
        return $this->expectedDeliveryDate;
    }

    public function setExpectedDeliveryDate(?\DateTimeImmutable $expectedDeliveryDate): static
    {
        $this->expectedDeliveryDate = $expectedDeliveryDate;

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): static
    {
        $this->comments = $comments;

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

    /**
     * @return Collection<int, SupplierOrderItem>
     */
    public function getSupplierOrderItems(): Collection
    {
        return $this->supplierOrderItems;
    }

    public function addSupplierOrderItem(SupplierOrderItem $supplierOrderItem): static
    {
        if (!$this->supplierOrderItems->contains($supplierOrderItem)) {
            $this->supplierOrderItems->add($supplierOrderItem);
            $supplierOrderItem->setSupplierOrder($this);
        }

        return $this;
    }

    public function removeSupplierOrderItem(SupplierOrderItem $supplierOrderItem): static
    {
        if ($this->supplierOrderItems->removeElement($supplierOrderItem)) {
            // set the owning side to null (unless already changed)
            if ($supplierOrderItem->getSupplierOrder() === $this) {
                $supplierOrderItem->setSupplierOrder(null);
            }
        }

        return $this;
    }
}
