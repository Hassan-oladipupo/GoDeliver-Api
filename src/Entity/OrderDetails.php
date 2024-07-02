<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderDetailsRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderDetailsRepository::class)]
class OrderDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups("order")]
    private ?int $orderId = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Groups("order")]
    private ?string $customerName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Groups("order")]
    private ?string $pickupContactNo = null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank()]
    #[Groups("order")]
    private ?string $pickupAddress = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Groups("order")]
    private ?string $deliveryFee = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("order")]
    private ?string $landMark = null;

    #[ORM\Column(length: 255)]
    #[Groups("order")]
    private ?string $state = null;

    #[ORM\Column(length: 255)]
    #[Groups("order")]
    private ?string $location = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("order")]
    private ?string $apartmentNumber = null;

    #[ORM\ManyToOne(targetEntity: RiderDetails::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Groups("order")]
    private ?RiderDetails $rider = null;


    #[ORM\Column(type: 'datetime')]
    #[Groups("order")]
    private ?DateTime $startTime = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups("order")]

    private ?DateTime $endTime = null;

    #[ORM\Column(length: 255)]

    #[Groups("order")]
    private ?string $orderStatus = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("order")]
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

    #[Groups(['order:read'])]
    public function getRider(): ?RiderDetails
    {
        return $this->rider;
    }
    public function setRider(?RiderDetails $rider): static
    {
        $this->rider = $rider;
        return $this;
    }

    #[Groups(['order:read'])]
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

    public function getOrderStatus(): ?string
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(string $status): static
    {
        $this->orderStatus = $status;
        return $this;
    }
}
