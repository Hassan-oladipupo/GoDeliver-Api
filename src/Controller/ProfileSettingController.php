<?php

namespace App\Controller;

use App\Entity\UserProfile;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileSettingController extends AbstractController
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    #[Route('api/v1/user-profile', name: 'app_user_profile', methods: ['POST'])]
    public function profileSetting(Request $request,  SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, LoggerInterface $logger): JsonResponse
    {
        try {
            /** @var User $user */
            $user = $this->getUser();

            $getUserProfile = $user->getUserProfile() ?? new UserProfile();

            $data = $request->getContent();
            $userProfile = $serializer->deserialize($data, UserProfile::class, 'json');

            $getUserProfile->setFirstName($userProfile->getFirstName());
            $getUserProfile->setLastName($userProfile->getLastName());
            $getUserProfile->setDateOfBirth($userProfile->getDateOfBirth());

            $errors = $validator->validate($getUserProfile);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return $this->json(['errors' => $errorMessages], 422);
            }

            $user->setUserProfile($getUserProfile);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($getUserProfile);
            $entityManager->flush();

            $ProfileData = [
                'firstName' => $userProfile->getFirstName(),
                'lastName' => $userProfile->getLastName(),
                'dateOfBirth' => $userProfile->getDateOfBirth()->format('Y-m-d'),
            ];


            return new JsonResponse(['message' => 'Your user profile settings were saved',  'profileData' => $ProfileData]);
        } catch (\Exception $e) {
            $logger->error('An error occurred: ' . $e->getMessage());
            return new JsonResponse(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }



    #[Route('api/v1/user-profile', name: 'app_get_user_profile', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function retrieveUserProfile(): JsonResponse
    {
        try {
            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
                return new JsonResponse(['message' => 'User is not authenticated'], 401);
            }

            /** @var User $user */
            $user = $this->getUser();
            $userProfile = $user->getUserProfile();

            if (!$userProfile) {
                return new JsonResponse(['message' => 'User profile not found'], 404);
            }

            $profileData = [
                'firstName' => $userProfile->getFirstName(),
                'lastName' => $userProfile->getLastName(),
                'dateOfBirth' => $userProfile->getDateOfBirth()->format('Y-m-d'),
                'profileImage' => $userProfile->getProfileImage()
                    ? $this->getParameter('profiles_directory') . '/' . $userProfile->getProfileImage()
                    : null,
            ];

            return new JsonResponse($profileData);
        } catch (\Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage());
            return new JsonResponse(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('api/v1/user-profile/update', name: 'app_update_user_profile', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function updateUserProfile(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, LoggerInterface $logger): JsonResponse
    {
        try {
            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
                return new JsonResponse(['message' => 'User is not authenticated'], 401);
            }

            /** @var User $user */
            $user = $this->getUser();
            $userProfile = $user->getUserProfile();

            if (!$userProfile) {
                return new JsonResponse(['message' => 'User profile not found'], 404);
            }

            $data = $request->getContent();
            $updatedProfile = $serializer->deserialize($data, UserProfile::class, 'json');

            $userProfile->setFirstName($updatedProfile->getFirstName());
            $userProfile->setLastName($updatedProfile->getLastName());
            $userProfile->setDateOfBirth($updatedProfile->getDateOfBirth());

            $errors = $validator->validate($userProfile);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return $this->json(['errors' => $errorMessages], 422);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($userProfile);
            $entityManager->flush();

            $updatedProfileData = [
                'firstName' => $userProfile->getFirstName(),
                'lastName' => $userProfile->getLastName(),
                'dateOfBirth' => $userProfile->getDateOfBirth()->format('Y-m-d'),
            ];

            return new JsonResponse(['message' => 'Your user profile has been updated', 'updatedProfile' => $updatedProfileData]);
        } catch (\Exception $e) {
            $logger->error('An error occurred: ' . $e->getMessage());
            return new JsonResponse(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/v1/profile-image', name: 'app_settings_profile_image', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage(Request $request, SluggerInterface $slugger, UserRepository $repo, ValidatorInterface $validator): JsonResponse
    {
        $profileImageFile = $request->files->get('Image');

        if (!$profileImageFile) {
            return new JsonResponse(['error' => 'No image uploaded.'], 400);
        }

        $constraints = [
            new File([
                'maxSize' => '1024k',
                'mimeTypes' => ['image/jpeg', 'image/png'],
                'mimeTypesMessage' => 'Please upload a valid PNG/JPEG image',
            ]),
        ];

        $violations = $validator->validate($profileImageFile, $constraints);

        if (count($violations) > 0) {
            return new JsonResponse(['error' => $violations[0]->getMessage()], 400);
        }

        try {
            $originalFileName = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFileName);
            $newFileName = $safeFilename . '-' . uniqid() . '.' . $profileImageFile->guessExtension();

            $profileImageFile->move(
                $this->getParameter('profiles_directory'),
                $newFileName
            );

            /** @var User $user */
            $user = $this->getUser();

            $profile = $user->getUserProfile() ?? new UserProfile();
            $profile->setProfileImage($newFileName);
            $user->setUserProfile($profile);

            $repo->save($user, true);

            $this->addFlash('success', 'Your profile image was updated');

            return new JsonResponse(['message' => 'Your profile image was uploaded successfully']);
        } catch (FileException $e) {
            $this->logger->error('Failed to upload profile image: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Failed to upload profile image.'], 500);
        }
    }
}
