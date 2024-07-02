<?php

namespace App\Controller;

use Exception;
use App\Entity\RiderDetails;
use Psr\Log\LoggerInterface;
use App\Repository\OrderDetailsRepository;
use App\Repository\RiderDetailsRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

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
            $addRider = $serializer->deserialize($data, RiderDetails::class, 'json', [AbstractNormalizer::GROUPS => ['order:write']]);
            $errors = $validator->validate($addRider);


            if (count($errors) > 0) {
                return $this->json(['errors' => (string) $errors], 422);
            }

            $repo->save($addRider, true);

            $responseData = $serializer->serialize($addRider, 'json', [AbstractNormalizer::GROUPS => ['rider']]);

            return new JsonResponse(['message' => 'Rider Added Successfully', 'rider_data' => json_decode($responseData)], 200);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    #[Route('/api/v1/riders', name: 'app_get_riders', methods: ['GET'])]
    public function retrieveAllRiders(
        RiderDetailsRepository $repo,
        SerializerInterface $serializer
    ): JsonResponse {
        try {
            $existingRider = $repo->findAll();
            $responseData = $serializer->serialize($existingRider, 'json', [
                AbstractNormalizer::GROUPS => ['rider'],
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true
            ]);

            return new JsonResponse(['message' => 'Riders Fetched Successfully', 'rider_data' => json_decode($responseData)], 200);
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
            $existingRider = $repo->findById($id);

            if (!$existingRider) {
                return $this->json(['message' => 'Rider not found'], 404);
            }

            $this->logger->info('Fetched Rider Details', ['rider' => $existingRider]);

            $data = $request->getContent();
            $serializer->deserialize($data, RiderDetails::class, 'json', [AbstractNormalizer::GROUPS => ['rider:write']]);

            $errors = $validator->validate($existingRider);

            if (count($errors) > 0) {
                return $this->json(['errors' => (string) $errors], 422);
            }

            $repo->save($existingRider, true);

            $responseData = $serializer->serialize($existingRider, 'json', [
                AbstractNormalizer::GROUPS => ['rider:read', 'order:read'],
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true
            ]);


            $this->logger->info('Serialized Rider Details', ['responseData' => $responseData]);

            return new JsonResponse(['message' => 'Rider Updated Successfully', 'rider_data' => json_decode($responseData)], 200);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/v1/riders/{id}', name: 'app_get_single_rider', methods: ['GET'])]
    public function retrieveRiderById(int $id, RiderDetailsRepository $repo, SerializerInterface $serializer, LoggerInterface $logger): JsonResponse
    {
        try {
            $existingRider = $repo->find($id);

            if (!$existingRider) {
                return new JsonResponse(['message' => 'Rider not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            $responseData = $serializer->serialize($existingRider, 'json', [
                AbstractNormalizer::GROUPS => ['rider'],
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            ]);

            return new JsonResponse([
                'message' => 'Rider Fetched Successfully',
                'rider_data' => json_decode($responseData)
            ], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            $logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => 'An error occurred: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
