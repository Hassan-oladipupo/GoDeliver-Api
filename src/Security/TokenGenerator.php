<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TokenGenerator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generateConfirmEmailToken(User $user): string
    {
        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $user->setResetToken($token);

        $expiresAt = new \DateTime();
        $expiresAt->modify('+2 hour');
        $user->setResetTokenExpiresAt($expiresAt);

        $this->entityManager->flush();

        return $token;
    }
}
