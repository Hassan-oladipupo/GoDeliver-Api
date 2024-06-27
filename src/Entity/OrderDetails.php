<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderDetailsRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderDetailsRepository::class)]
class OrderDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $orderId = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $customerName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $pickupContactNo = null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank()]
    private ?string $pickupAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $landMark = null;

    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apartmentNumber = null;

    #[ORM\ManyToOne(targetEntity: RiderDetails::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    private ?RiderDetails $rider = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $deliveryFee = null;


    #[ORM\Column(type: 'datetime')]
    private ?DateTime $startTime = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $endTime = null;

    #[ORM\Column(length: 255)]
    private ?string $orderStatus = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    public function __construct()
    {
        $this->startTime = new DateTime();
        $this->orderStatus = 'pending';
    }



    public function getOrderId(): ?int
    {
        return $this->orderId;
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

    public function getDeliveryFee(): ?string
    {
        return $this->deliveryFee;
    }

    public function setDeliveryFee(string $deliveryFee): static
    {
        $this->deliveryFee = $deliveryFee;
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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;
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



    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }


    public function getStartTime(): ?DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(DateTime $startTime): static
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(?DateTime $endTime): static
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getOderStatus(): ?string
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(string $status): static
    {
        $this->orderStatus = $status;
        return $this;
    }
}
