<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DeliveryLocationPriceRepository;

#[ORM\Entity(repositoryClass: DeliveryLocationPriceRepository::class)]
class DeliveryLocationPrice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $state = null;
    #[ORM\Column(length: 255)]
    private ?string $location = null;
    #[ORM\Column(length: 255)]
    private ?string $deliveryFee = null;





    public function getId(): ?int
    {
        return $this->id;
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

    public function getDeliveryFee(): ?string
    {
        return $this->deliveryFee;
    }

    public function setDeliveryFee(string $deliveryFee): static
    {
        $this->deliveryFee = $deliveryFee;
        return $this;
    }
}
