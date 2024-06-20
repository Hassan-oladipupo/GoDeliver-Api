<?php

namespace App\Controller;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


class UserController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    #[Route('/api/v1/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserRepository $repo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['username']) || empty($data['password'])) {
            return new JsonResponse(['message' => 'Email and password are required.'], 400);
        }

        $email = $data['username'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['message' => 'Invalid email format.'], 400);
        }

        $password = $data['password'];

        if (!$this->isValidPassword($password)) {
            return new JsonResponse(['message' => 'Weak password. Password must contain at least 8 characters including uppercase, lowercase and digit'], 400);
        }

        $existingUser = $repo->findOneBy(['username' => $email]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'Email is already registered.'], 400);
        }

        $user = new User();
        $user->setUserName($email);

        $confirmationToken = mt_rand(100000, 999999);
        $user->setConfirmationToken($confirmationToken);

        $hashedPassword = $userPasswordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        if (!empty($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }
        if (!empty($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['message' => 'Validation errors', 'errors' => $errorMessages], 400);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'User registered successfully.',
            'confirmation_token' => $confirmationToken,
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ], 201);
    }

    #[Route('/api/v1/confirm-email', name: 'api_confirm_email', methods: ['POST'])]
    public function confirmEmail(Request $request, UserRepository $repo, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $requestData = json_decode($request->getContent(), true);

            if (empty($requestData) || !isset($requestData['confirmation_token'])) {
                return new JsonResponse(['message' => 'Invalid token.'], 400);
            }

            $token = $requestData['confirmation_token'];

            $user = $repo->findOneBy(['confirmationToken' => $token]);

            if (!$user) {
                return new JsonResponse(['message' => 'User not found.'], 404);
            }

            if ($user->isConfirmed()) {
                return new JsonResponse(['message' => 'Email is already confirmed.'], 400);
            }

            $user->setConfirmed(true);
            $user->setConfirmationToken(null);

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Email confirmed successfully.'], 200);
        } catch (\Exception $e) {
            $this->logger->error('An error occurred during email confirmation: ' . $e->getMessage());

            return new JsonResponse(['message' => 'An error occurred during email confirmation: ' . $e->getMessage()], 500);
        }
    }


    #[Route('/api/v1/login', name: 'app_auth_login', methods: ['POST'])]
    public function login(AuthenticationUtils $authenticationUtils, Security $security, JWTTokenManagerInterface $jwtManager): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {
            $errorMessage = 'Incorrect login details.';

            if (str_contains($error->getMessageKey(), 'Invalid credentials')) {
                $errorMessage = 'Incorrect password.';
            }

            return new JsonResponse(['error' => $errorMessage], 401);
        }
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }
        $userId = $user->getId();
        $token = $jwtManager->create($user, ['user_id' => $userId]);

        return $this->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ]);
    }

    #[Route('/api/v1/logout', name: 'app_auth_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return new JsonResponse(['message' => 'Logout successful.']);
    }



    private function isValidPassword($password)
    {
        $minLength = 8;
        $hasUpperCase = preg_match('/[A-Z]/', $password);
        $hasLowerCase = preg_match('/[a-z]/', $password);
        $hasDigits = preg_match('/\d/', $password);

        return (
            strlen($password) >= $minLength &&
            $hasUpperCase &&
            $hasLowerCase &&
            $hasDigits
        );
    }
}
