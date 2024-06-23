<?php

namespace App\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager as BaseJWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTManager extends BaseJWTManager
{
    private $eventDispatcher;

    public function __construct(JWTEncoderInterface $jwtEncoder, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($jwtEncoder, $eventDispatcher);
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createJwt(UserInterface $user): string
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('User must be an instance of App\Entity\User');
        }

        $userProfile = $user->getUserProfile();

        $payload = [
            'user_id' => $user->getId(),
            'username' => $user->getUserIdentifier(),
            'profileImage' => $userProfile ? $userProfile->getProfileImage() : null,

        ];

        $jwtEvent = new JWTCreatedEvent($payload, $user);
        $this->eventDispatcher->dispatch($jwtEvent, Events::JWT_CREATED);

        return $this->jwtEncoder->encode($jwtEvent->getData());
    }
}
