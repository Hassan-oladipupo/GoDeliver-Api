<?php

namespace App\Entity;

use App\Repository\OrderDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderDetailsRepository::class)]
class OrderDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $customerName = null;

    #[ORM\Column(length: 255)]
    private ?string $pickupContactNo = null;

    #[ORM\Column(length: 500)]
    private ?string $pickupAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $landMark = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Area = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apartmentNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): static
    {
        $this->customerName = $customerName;

        return $this;
    }

    public function getPickupContactNo(): ?string
    {
        return $this->pickupContactNo;
    }

    public function setPickupContactNo(string $pickupContactNo): static
    {
        $this->pickupContactNo = $pickupContactNo;

        return $this;
    }

    public function getPickupAddress(): ?string
    {
        return $this->pickupAddress;
    }

    public function setPickupAddress(string $pickupAddress): static
    {
        $this->pickupAddress = $pickupAddress;

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
}
