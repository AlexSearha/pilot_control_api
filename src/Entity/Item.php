<?php

namespace App\Entity;

use App\Enum\ItemStatusEnum;
use App\Enum\ItemUnitEnum;
use App\Repository\ItemRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?Company $company = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?Supplier $supplier = null;

    #[ORM\Column(enumType: ItemStatusEnum::class)]
    private ?ItemStatusEnum $status = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quatityReserved = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantityAlertThreshold = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $serialNumber = null;

    #[ORM\Column(enumType: ItemUnitEnum::class)]
    private ?ItemUnitEnum $unit = null;

    #[ORM\Column(nullable: true, type: Types::DECIMAL)]
    private ?float $price = null;

    #[ORM\Column]
    private ?bool $obsolet = null;

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
     * @var Collection<int, Maintenance>
     */
    #[ORM\OneToMany(targetEntity: Maintenance::class, mappedBy: 'item')]
    private Collection $maintenances;

    /**
     * @var Collection<int, SupplierOrderItem>
     */
    #[ORM\OneToMany(targetEntity: SupplierOrderItem::class, mappedBy: 'item')]
    private Collection $supplierOrderItems;

    /**
     * @var Collection<int, QuotationItem>
     */
    #[ORM\OneToMany(targetEntity: QuotationItem::class, mappedBy: 'item')]
    private Collection $quotationItems;

    /**
     * @var Collection<int, InvoiceItem>
     */
    #[ORM\OneToMany(targetEntity: InvoiceItem::class, mappedBy: 'item')]
    private Collection $invoiceItems;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, inversedBy: 'items')]
    private Collection $project;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?Currency $currency = null;

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

    #[ORM\PrePersist]
    public function setObsoletAtInit() : void
    {
        if ($this->obsolet === null) {
            $this->obsolet = false;
        }
    }

    public function __construct()
    {
        $this->maintenances = new ArrayCollection();
        $this->supplierOrderItems = new ArrayCollection();
        $this->quotationItems = new ArrayCollection();
        $this->invoiceItems = new ArrayCollection();
        $this->project = new ArrayCollection();
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

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

    public function getStatus(): ?ItemStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ItemStatusEnum $status): static
    {
        $this->status = $status;

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuatityReserved(): ?int
    {
        return $this->quatityReserved;
    }

    public function setQuatityReserved(?int $quatityReserved): static
    {
        $this->quatityReserved = $quatityReserved;

        return $this;
    }

    public function getQuantityAlertThreshold(): ?int
    {
        return $this->quantityAlertThreshold;
    }

    public function setQuantityAlertThreshold(?int $quantityAlertThreshold): static
    {
        $this->quantityAlertThreshold = $quantityAlertThreshold;

        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(?string $serialNumber): static
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getUnit(): ?ItemUnitEnum
    {
        return $this->unit;
    }

    public function setUnit(?ItemUnitEnum $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isObsolet(): ?bool
    {
        return $this->obsolet;
    }

    public function setObsolet(?bool $obsolet): static
    {
        $this->obsolet = $obsolet;

        return $this;
    }

    /**
     * @return Collection<int, Maintenance>
     */
    public function getMaintenances(): Collection
    {
        return $this->maintenances;
    }

    public function addMaintenance(Maintenance $maintenance): static
    {
        if (!$this->maintenances->contains($maintenance)) {
            $this->maintenances->add($maintenance);
            $maintenance->setItem($this);
        }

        return $this;
    }

    public function removeMaintenance(Maintenance $maintenance): static
    {
        if ($this->maintenances->removeElement($maintenance)) {
            // set the owning side to null (unless already changed)
            if ($maintenance->getItem() === $this) {
                $maintenance->setItem(null);
            }
        }

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
            $supplierOrderItem->setItem($this);
        }

        return $this;
    }

    public function removeSupplierOrderItem(SupplierOrderItem $supplierOrderItem): static
    {
        if ($this->supplierOrderItems->removeElement($supplierOrderItem)) {
            // set the owning side to null (unless already changed)
            if ($supplierOrderItem->getItem() === $this) {
                $supplierOrderItem->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QuotationItem>
     */
    public function getQuotationItems(): Collection
    {
        return $this->quotationItems;
    }

    public function addQuotationItem(QuotationItem $quotationItem): static
    {
        if (!$this->quotationItems->contains($quotationItem)) {
            $this->quotationItems->add($quotationItem);
            $quotationItem->setItem($this);
        }

        return $this;
    }

    public function removeQuotationItem(QuotationItem $quotationItem): static
    {
        if ($this->quotationItems->removeElement($quotationItem)) {
            // set the owning side to null (unless already changed)
            if ($quotationItem->getItem() === $this) {
                $quotationItem->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InvoiceItem>
     */
    public function getInvoiceItems(): Collection
    {
        return $this->invoiceItems;
    }

    public function addInvoiceItem(InvoiceItem $invoiceItem): static
    {
        if (!$this->invoiceItems->contains($invoiceItem)) {
            $this->invoiceItems->add($invoiceItem);
            $invoiceItem->setItem($this);
        }

        return $this;
    }

    public function removeInvoiceItem(InvoiceItem $invoiceItem): static
    {
        if ($this->invoiceItems->removeElement($invoiceItem)) {
            // set the owning side to null (unless already changed)
            if ($invoiceItem->getItem() === $this) {
                $invoiceItem->setItem(null);
            }
        }

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

    public function getupdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getdeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setdeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProject(): Collection
    {
        return $this->project;
    }

    public function addProject(Project $project): static
    {
        if (!$this->project->contains($project)) {
            $this->project->add($project);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        $this->project->removeElement($project);

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

}
