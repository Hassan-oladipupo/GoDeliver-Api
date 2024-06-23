<?php

namespace App\Security;

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
        $payload = [
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'exp' => time() + 3600,
        ];

        $jwtEvent = new JWTCreatedEvent($payload, $user);
        $this->eventDispatcher->dispatch($jwtEvent, Events::JWT_CREATED);

        return $this->jwtEncoder->encode($jwtEvent->getData());
    }
}
