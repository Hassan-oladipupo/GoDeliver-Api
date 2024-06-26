<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\JWTManager;
use Psr\Log\LoggerInterface;
use App\Service\TokenGenerator;
use App\Repository\UserRepository;
use App\Security\PasswordHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class UserController extends AbstractController
{
    private $logger;
    private $entityManager;
    private $jwtManager;
    private $tokenService;
    private $isValidPassword;




    public function __construct(TokenGenerator $tokenService, EntityManagerInterface $entityManager, JWTManager $jwtManager, LoggerInterface $logger, PasswordHelper $isValidPassword)
    {
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->logger = $logger;
        $this->tokenService = $tokenService;
        $this->isValidPassword = $isValidPassword;
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
        $validPassword = $this->isValidPassword->validatePassword($password);
        if (!$validPassword) {
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
        if (!empty($data['roles'])) {
            $user->setRoles($data['roles']);
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
            'user_roles' => $user->getRoles(),
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
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {
            $errorMessage = 'Incorrect login details.';

            if (str_contains($error->getMessageKey(), 'Invalid credentials')) {
                $errorMessage = 'Incorrect password.';
            }

            return new JsonResponse(['error' => $errorMessage], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Username and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        $username = $data['username'];
        $password = $data['password'];

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user || !password_verify($password, $user->getPassword())) {
            return $this->json(['error' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtManager->createJwt($user);

        return $this->json([
            'message' => 'Login successful',
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'access_token' => $token,
            'user_roles' => $user->getRoles(),
        ]);
    }

    #[Route('/api/v1/logout', name: 'app_auth_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return new JsonResponse(['message' => 'Logout successful.']);
    }



    #[Route('/api/v1/forgot-password', name: 'app_forgot_password', methods: ['POST'])]
    public function forgotPassword(Request $request): JsonResponse
    {
        $email = json_decode($request->getContent(), true);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $token = $this->tokenService->generateConfirmEmailToken($user);


        return new JsonResponse(['message' => 'Password reset token generated.', 'reset_Password_token' => $token], JsonResponse::HTTP_OK);
    }



    #[Route('/api/v1/reset-password', name: 'app_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request, UserPasswordHasherInterface $userPasswordHasher,): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $token = $data['token'];
        $newPassword = $data['new_password'];
        $confirmPassword = $data['confirm_password'];

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user || $user->getResetTokenExpiresAt() < new \DateTime()) {
            return new JsonResponse(['error' => 'Invalid or expired token.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $validPassword = $this->isValidPassword->validatePassword($newPassword);
        if (!$validPassword) {
            return new JsonResponse(['message' => 'Weak password. Password must contain at least 8 characters including uppercase, lowercase and digit'], 400);
        }

        if ($newPassword != $confirmPassword) {
            return new JsonResponse(['message' => 'New password and confirm password do not match'], 400);
        }

        $hashedPassword = $userPasswordHasher->hashPassword($user, $newPassword);


        $user->setPassword($hashedPassword);


        $user->setResetToken(null);

        $user->setResetTokenExpiresAt(null);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Password reset successful.'], JsonResponse::HTTP_OK);
    }
}
