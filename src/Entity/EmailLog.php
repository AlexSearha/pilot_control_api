<?php

namespace App\Entity;

use App\Repository\EmailLogRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EmailLogRepository::class)]
class EmailLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'emailLogs')]
    private ?SmtpConfiguration $smtpConfiguration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $toEmail = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $ccEmail = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $bccEmail = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $attachement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $errorMessage = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne(inversedBy: 'emailLogs')]
    private ?Company $company = null;

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
        if($this->createdAt === null) {
            $dateTimeNow = new DateTimeImmutable();
            $this->createdAt = $dateTimeNow;
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

    public function getSmtpConfiguration(): ?SmtpConfiguration
    {
        return $this->smtpConfiguration;
    }

    public function setSmtpConfiguration(?SmtpConfiguration $smtpConfiguration): static
    {
        $this->smtpConfiguration = $smtpConfiguration;

        return $this;
    }

    public function getToEmail(): ?string
    {
        return $this->toEmail;
    }

    public function setToEmail(?string $toEmail): static
    {
        $this->toEmail = $toEmail;

        return $this;
    }

    public function getCcEmail(): ?string
    {
        return $this->ccEmail;
    }

    public function setCcEmail(?string $ccEmail): static
    {
        $this->ccEmail = $ccEmail;

        return $this;
    }

    public function getBccEmail(): ?string
    {
        return $this->bccEmail;
    }

    public function setBccEmail(?string $bccEmail): static
    {
        $this->bccEmail = $bccEmail;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getAttachement()
    {
        return $this->attachement;
    }

    public function setAttachement($attachement): static
    {
        $this->attachement = $attachement;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): static
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeImmutable $sentAt): static
    {
        $this->sentAt = $sentAt;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

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
}
