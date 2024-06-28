<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use App\Entity\UserDeliveryDetails;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserDeliveryDetailsController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('api/v1/delivery-details', name: 'app_user_delivery_details', methods: ['POST'])]
    public function addDeliveryDetails(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, LoggerInterface $logger): JsonResponse
    {
        try {
            /** @var User $user */
            $user = $this->getUser();

            $getDeliveryDetails = $user->getUserDeliveryDetails() ?? new UserDeliveryDetails();

            $data = $request->getContent();
            $userDeliveryDetails = $serializer->deserialize($data, UserDeliveryDetails::class, 'json');

            $getDeliveryDetails->setRecipientPhoneNumber($userDeliveryDetails->getRecipientPhoneNumber());
            $getDeliveryDetails->setRecipientAddress($userDeliveryDetails->getRecipientAddress());
            $getDeliveryDetails->setCategory($userDeliveryDetails->getCategory());
            $getDeliveryDetails->setLandMark($userDeliveryDetails->getLandMark());
            $getDeliveryDetails->setArea($userDeliveryDetails->getArea());
            $getDeliveryDetails->setApartmentNumber($userDeliveryDetails->getApartmentNumber());
            $getDeliveryDetails->setUser($user);

            $errors = $validator->validate($getDeliveryDetails);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return $this->json(['errors' => $errorMessages], 422);
            }

            $user->setUserDeliveryDetails($getDeliveryDetails);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($getDeliveryDetails);
            $entityManager->flush();

            $deliveryData = [
                'recipientContact' => $getDeliveryDetails->getRecipientPhoneNumber(),
                'recipient_address' => $getDeliveryDetails->getRecipientAddress(),
                'category' => $getDeliveryDetails->getCategory(),
                'land_mark' => $getDeliveryDetails->getLandMark(),
                'area' => $getDeliveryDetails->getArea(),
                'apartment' => $getDeliveryDetails->getApartmentNumber(),
                'user_id' => $getDeliveryDetails->getUser()->getId(),
            ];

            return new JsonResponse(['message' => 'Your delivery details were saved',  'deliveryData' =>  $deliveryData], 201);
        } catch (\Exception $e) {
            $logger->error('An error occurred: ' . $e->getMessage());
            return new JsonResponse(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('api/v1/delivery-details', name: 'app_get_user_delivery_details', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function retrieveUserProfile(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = $this->getUser();
            $userDeliveryDetails = $user->getUserDeliveryDetails();

            if (!$userDeliveryDetails) {
                return new JsonResponse(['message' => 'User delivery details not found'], 404);
            }

            $userDeliveryData = [
                'recipientContact' => $userDeliveryDetails->getRecipientPhoneNumber(),
                'recipient_address' => $userDeliveryDetails->getRecipientAddress(),
                'category' => $userDeliveryDetails->getCategory(),
                'land_mark' => $userDeliveryDetails->getLandMark(),
                'area' => $userDeliveryDetails->getArea(),
                'apartment' => $userDeliveryDetails->getApartmentNumber(),
                'user_id' => $userDeliveryDetails->getUser()->getId(),
            ];

            return new JsonResponse($userDeliveryData, 200);
        } catch (\Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage());
            return new JsonResponse(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/v1/delivery-details', name: 'app_update_user_profile', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function updateUserProfile(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, LoggerInterface $logger): JsonResponse
    {
        try {
            /** @var User $user */
            $user = $this->getUser();
            $getDeliveryDetails = $user->getUserDeliveryDetails();

            if (!$getDeliveryDetails) {
                return new JsonResponse(['message' => 'No delivery details is found for this user'], 200);
            }

            $data = $request->getContent();
            $updatedDeliveryDetails = $serializer->deserialize($data, UserDeliveryDetails::class, 'json');

            $getDeliveryDetails->setRecipientPhoneNumber($updatedDeliveryDetails->getRecipientPhoneNumber());
            $getDeliveryDetails->setRecipientAddress($updatedDeliveryDetails->getRecipientAddress());
            $getDeliveryDetails->setCategory($updatedDeliveryDetails->getCategory());
            $getDeliveryDetails->setLandMark($updatedDeliveryDetails->getLandMark());
            $getDeliveryDetails->setArea($updatedDeliveryDetails->getArea());
            $getDeliveryDetails->setApartmentNumber($updatedDeliveryDetails->getApartmentNumber());
            $getDeliveryDetails->setUser($user);

            $errors = $validator->validate($getDeliveryDetails);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return $this->json(['errors' => $errorMessages], 422);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($getDeliveryDetails);
            $entityManager->flush();

            $updatedDeliveryData = [
                'recipientContact' =>  $getDeliveryDetails->getRecipientPhoneNumber(),
                'recipient_address' =>  $getDeliveryDetails->getRecipientAddress(),
                'category' =>  $getDeliveryDetails->getCategory(),
                'land_mark' =>  $getDeliveryDetails->getLandMark(),
                'area' =>  $getDeliveryDetails->getArea(),
                'apartment' =>  $getDeliveryDetails->getApartmentNumber(),
                'user_id' =>  $getDeliveryDetails->getUser()->getId(),
            ];

            return new JsonResponse(['message' => 'Your  delivery has been updated', 'updatedProfile' =>   $updatedDeliveryData], 200);
        } catch (\Exception $e) {
            $logger->error('An error occurred: ' . $e->getMessage());
            return new JsonResponse(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
