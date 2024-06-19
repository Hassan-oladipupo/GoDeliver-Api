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
    private ?string $area = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apartmentNumber = null;

    #[ORM\ManyToOne(targetEntity: RiderDetails::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?RiderDetails $rider = null;

    #[ORM\OneToOne(inversedBy: 'order', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?DeliveryDetails $deliveryDetails = null;

    #[ORM\ManyToOne(inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

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
        return $this->area;
    }

    public function setArea(?string $area): static
    {
        $this->area = $area;
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

    public function getRider(): ?RiderDetails
    {
        return $this->rider;
    }

    public function setRider(?RiderDetails $rider): static
    {
        $this->rider = $rider;
        return $this;
    }

    public function getDelivery(): ?DeliveryDetails
    {
        return $this->deliveryDetails;
    }

    public function setDelivery(?DeliveryDetails $delivery): static
    {
        $this->deliveryDetails = $delivery;
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
