<?php

namespace App\Entity;

use DateTime;
use App\Repository\DeliveryDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeliveryDetailsRepository::class)]
class DeliveryDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;



    #[ORM\Column(type: 'datetime')]
    private ?DateTime $startTime = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $endTime = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;


    #[ORM\Column(length: 255)]
    private ?string $deliveryFee = null;

    public function __construct()
    {
        $this->startTime = new DateTime();
    }


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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
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
