<?php

namespace App\Controller;

use Exception;
use App\Entity\RiderDetails;
use Psr\Log\LoggerInterface;
use App\Repository\RiderDetailsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RidersController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/api/v1/riders', name: 'app_add_riders', methods: ['POST'])]
    public function addRider(
        Request $request,
        RiderDetailsRepository $repo,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Access denied'], 403);
        }

        try {
            $data = $request->getContent();
            $addRider = $serializer->deserialize($data, RiderDetails::class, 'json');
            $errors = $validator->validate($addRider);

            if (count($errors) > 0) {
                return $this->json(['errors' => (string) $errors], 422);
            }

            $repo->save($addRider, true);

            return $this->json(['message' => 'Rider Added Successfully', 'rider_data' => $addRider], 201);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/v1/riders', name: 'app_get_riders', methods: ['GET'])]
    public function retrieveAllRiders(RiderDetailsRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Access denied'], 403);
        }

        try {
            $riders = $repo->findAll();
            $riderData = $serializer->serialize($riders, 'json', [
                AbstractNormalizer::ATTRIBUTES => [
                    'id', 'riderName', 'riderContactNo', 'vehicleDetails', 'currentLocation',
                    'orders' => ['id', 'customerName', 'pickupContactNo', 'pickupAddress']
                ],
            ]);

            return new JsonResponse(json_decode($riderData, true), 200);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/v1/riders/{id}', name: 'app_update_riders', methods: ['PUT'])]
    public function updateRider(
        int $id,
        Request $request,
        RiderDetailsRepository $repo,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Access denied'], 403);
        }

        try {
            $existingRider = $repo->find($id);

            if (!$existingRider) {
                return $this->json(['message' => 'Rider not found'], 404);
            }

            $data = $request->getContent();
            $serializer->deserialize($data, RiderDetails::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $existingRider]);

            $errors = $validator->validate($existingRider);

            if (count($errors) > 0) {
                return $this->json(['errors' => (string) $errors], 422);
            }

            $repo->save($existingRider, true);

            return $this->json(['message' => 'Rider Updated Successfully', 'rider_data' => $existingRider], 200);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/v1/riders/{id}', name: 'app_get_single_riders', methods: ['GET'])]
    public function retrieveRiderById(
        int $id,
        RiderDetailsRepository $repo,
    ): JsonResponse {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Access denied'], 403);
        }

        try {
            $existingRider = $repo->find($id);

            if (!$existingRider) {
                return $this->json(['message' => 'Rider not found'], 404);
            }

            return $this->json(['message' => 'Rider Updated Successfully', 'rider_details' => $existingRider], 200);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
