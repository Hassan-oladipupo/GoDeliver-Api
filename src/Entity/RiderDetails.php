<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RiderDetailsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: RiderDetailsRepository::class)]
class RiderDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $riderName = null;

    #[ORM\Column(length: 255)]
    private ?string $riderContactNo = null;

    #[ORM\Column(length: 255)]
    private ?string $vehicleDetails = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $currentLocation = null;

    #[ORM\OneToMany(mappedBy: 'rider', targetEntity: OrderDetails::class)]
    private Collection $orders;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $bannedUntill = null;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRiderName(): ?string
    {
        return $this->riderName;
    }

    public function setRiderName(string $riderName): static
    {
        $this->riderName = $riderName;

        return $this;
    }

    public function getRiderContactNo(): ?string
    {
        return $this->riderContactNo;
    }

    public function setRiderContactNo(string $riderContactNo): static
    {
        $this->riderContactNo = $riderContactNo;

        return $this;
    }

    public function getVehicleDetails(): ?string
    {
        return $this->vehicleDetails;
    }

    public function setVehicleDetails(string $vehicleDetails): static
    {
        $this->vehicleDetails = $vehicleDetails;

        return $this;
    }

    public function getCurrentLocation(): ?string
    {
        return $this->currentLocation;
    }

    public function setCurrentLocation(?string $currentLocation): static
    {
        $this->currentLocation = $currentLocation;

        return $this;
    }

    public function getOrderDetails(): Collection
    {
        return $this->orders;
    }

    public function addOrderDetails(OrderDetails $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setRider($this);
        }

        return $this;
    }

    public function removeOrder(OrderDetails $order): static
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getRider() === $this) {
                $order->setRider(null);
            }
        }

        return $this;
    }

    public function getBannedUntill(): ?\DateTimeInterface
    {
        return $this->bannedUntill;
    }

    public function setBannedUntill(?\DateTimeInterface $bannedUntill): static
    {
        $this->bannedUntill = $bannedUntill;

        return $this;
    }
}
