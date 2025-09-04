<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    #[ORM\Column(length: 4)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    /**
     * @var Collection<int, SupplierOrder>
     */
    #[ORM\OneToMany(targetEntity: SupplierOrder::class, mappedBy: 'currency')]
    private Collection $supplierOrders;

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

    /**
     * @var Collection<int, Quotation>
     */
    #[ORM\OneToMany(targetEntity: Quotation::class, mappedBy: 'currency')]
    private Collection $quotations;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'currency')]
    private Collection $items;

    public function __construct()
    {
        $this->supplierOrders = new ArrayCollection();
        $this->quotations = new ArrayCollection();
        $this->items = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
     * @return Collection<int, SupplierOrder>
     */
    public function getSupplierOrders(): Collection
    {
        return $this->supplierOrders;
    }

    public function addSupplierOrder(SupplierOrder $supplierOrder): static
    {
        if (!$this->supplierOrders->contains($supplierOrder)) {
            $this->supplierOrders->add($supplierOrder);
            $supplierOrder->setCurrency($this);
        }

        return $this;
    }

    public function removeSupplierOrder(SupplierOrder $supplierOrder): static
    {
        if ($this->supplierOrders->removeElement($supplierOrder)) {
            // set the owning side to null (unless already changed)
            if ($supplierOrder->getCurrency() === $this) {
                $supplierOrder->setCurrency(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quotation>
     */
    public function getQuotations(): Collection
    {
        return $this->quotations;
    }

    public function addQuotation(Quotation $quotation): static
    {
        if (!$this->quotations->contains($quotation)) {
            $this->quotations->add($quotation);
            $quotation->setCurrency($this);
        }

        return $this;
    }

    public function removeQuotation(Quotation $quotation): static
    {
        if ($this->quotations->removeElement($quotation)) {
            // set the owning side to null (unless already changed)
            if ($quotation->getCurrency() === $this) {
                $quotation->setCurrency(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setCurrency($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getCurrency() === $this) {
                $item->setCurrency(null);
            }
        }

        return $this;
    }
}
