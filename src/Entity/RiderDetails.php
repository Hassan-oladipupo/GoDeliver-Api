<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RiderDetailsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: RiderDetailsRepository::class)]
class RiderDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("rider")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("rider")]
    private ?string $riderName = null;

    #[ORM\Column(length: 255)]
    #[Groups("rider")]
    private ?string $riderContactNo = null;

    #[ORM\Column(length: 255)]
    #[Groups("rider")]
    private ?string $vehicleDetails = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("rider")]
    private ?string $currentLocation = null;

    #[ORM\OneToMany(mappedBy: 'rider', targetEntity: OrderDetails::class, cascade: ['persist', 'remove'], fetch: 'EAGER')]
    #[Groups("rider")]
    #[MaxDepth(1)]
    private Collection $orders;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("rider")]
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

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(OrderDetails $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
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
