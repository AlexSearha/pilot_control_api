<?php

namespace App\Entity;

use App\Enum\InvoiceStatusEnum;
use App\Repository\InvoiceRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    /**
     * @var Collection<int, InvoiceItem>
     */
    #[ORM\OneToMany(targetEntity: InvoiceItem::class, mappedBy: 'invoice')]
    private Collection $invoiceItems;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'invoice')]
    private Collection $payments;

    #[ORM\Column(length: 255)]
    private ?string $invoiceNumber = null;

    /**
     * Correspond à la date d'émission de la facture
     *
     * @var \DateTimeImmutable|null
     */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $issueDate = null;

        /**
         * Date d'écheance du paiement
     *
     * @var \DateTimeImmutable|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $dueDate = null;

    #[ORM\Column(enumType: InvoiceStatusEnum::class)]
    private ?InvoiceStatusEnum $status = null;

    #[ORM\Column(type:Types::DECIMAL)]
    private ?float $amountHt = null;

    #[ORM\Column(type:Types::DECIMAL)]
    private ?float $amountTtc = null;

    #[ORM\Column(type:Types::DECIMAL)]
    private ?float $taxRate = null;

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

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?Quotation $quotation = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?Company $company = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?CompanyClient $companyClient = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?Project $project = null;

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
        $this->invoiceItems = new ArrayCollection();
        $this->payments = new ArrayCollection();
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
            $invoiceItem->setInvoice($this);
        }

        return $this;
    }

    public function removeInvoiceItem(InvoiceItem $invoiceItem): static
    {
        if ($this->invoiceItems->removeElement($invoiceItem)) {
            // set the owning side to null (unless already changed)
            if ($invoiceItem->getInvoice() === $this) {
                $invoiceItem->setInvoice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setInvoice($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getInvoice() === $this) {
                $payment->setInvoice(null);
            }
        }

        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): static
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getIssueDate(): ?\DateTimeImmutable
    {
        return $this->issueDate;
    }

    public function setIssueDate(?\DateTimeImmutable $issueDate): static
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeImmutable $dueDate): static
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getStatus(): ?InvoiceStatusEnum
    {
        return $this->status;
    }

    public function setStatus(InvoiceStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAmountHt(): ?float
    {
        return $this->amountHt;
    }

    public function setAmountHt(float $amountHt): static
    {
        $this->amountHt = $amountHt;

        return $this;
    }

    public function getAmountTtc(): ?float
    {
        return $this->amountTtc;
    }

    public function setAmountTtc(float $amountTtc): static
    {
        $this->amountTtc = $amountTtc;

        return $this;
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setTaxRate(float $taxRate): static
    {
        $this->taxRate = $taxRate;

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

    public function getQuotation(): ?Quotation
    {
        return $this->quotation;
    }

    public function setQuotation(?Quotation $quotation): static
    {
        $this->quotation = $quotation;

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

    public function getCompanyClient(): ?CompanyClient
    {
        return $this->companyClient;
    }

    public function setCompanyClient(?CompanyClient $companyClient): static
    {
        $this->companyClient = $companyClient;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
