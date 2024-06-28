<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserDeliveryDetailsRepository;

#[ORM\Entity(repositoryClass: UserDeliveryDetailsRepository::class)]
class UserDeliveryDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $recipientPhoneNumber = null;

    #[ORM\Column(length: 500)]
    private ?string $recipientAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $landMark = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Area = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apartmentNumber = null;

    #[ORM\OneToOne(inversedBy: 'userDeliveryDetails', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipientPhoneNumber(): ?string
    {
        return $this->recipientPhoneNumber;
    }

    public function setRecipientPhoneNumber(string $recipientPhoneNumber): static
    {
        $this->recipientPhoneNumber = $recipientPhoneNumber;
        return $this;
    }

    public function getRecipientAddress(): ?string
    {
        return $this->recipientAddress;
    }

    public function setRecipientAddress(string $recipientAddress): static
    {
        $this->recipientAddress = $recipientAddress;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getLandMark(): ?string
    {
        return $this->landMark;
    }

    public function setLandMark(?string $landMark): static
    {
        $this->landMark = $landMark;
        return $this;
    }

    public function getArea(): ?string
    {
        return $this->Area;
    }

    public function setArea(?string $Area): static
    {
        $this->Area = $Area;
        return $this;
    }

    public function getApartmentNumber(): ?string
    {
        return $this->apartmentNumber;
    }

    public function setApartmentNumber(?string $apartmentNumber): static
    {
        $this->apartmentNumber = $apartmentNumber;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
}
