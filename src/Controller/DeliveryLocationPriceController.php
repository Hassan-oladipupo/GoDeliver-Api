<?php

namespace App\Controller;

use Exception;
use Psr\Log\LoggerInterface;
use App\Entity\DeliveryDetails;
use App\Entity\DeliveryLocationPrice;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\DeliveryLocationPriceRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeliveryLocationPriceController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/api/v1/location-price', name: 'app_delivery_location_price', methods: ['POST'])]
    public function addLocationPrice(
        Request $request,
        DeliveryLocationPriceRepository $repo,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Access denied'], 403);
        }

        try {
            $data = $request->getContent();
            $addLocationPrice = $serializer->deserialize($data, DeliveryLocationPrice::class, 'json');
            $errors = $validator->validate($addLocationPrice);

            if (count($errors) > 0) {
                return $this->json(['errors' => (string) $errors], 422);
            }

            $repo->save($addLocationPrice, true);

            return $this->json([
                'message' => 'Price Added Successfully',
                'location_price' => $addLocationPrice
            ], 201);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/v1/location-price', name: 'app_get_location_price', methods: ['GET'])]
    public function retrieveAllLocationPrice(DeliveryLocationPriceRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Access denied'], 403);
        }

        try {
            $locationPrice = $repo->findAll();
            $riderData = $serializer->serialize($locationPrice, 'json', [
                AbstractNormalizer::ATTRIBUTES => [
                    'id',  'state', 'location', 'deliveryFee',
                ],
            ]);

            return new JsonResponse(json_decode($riderData, true), 200);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/v1/location-price/{id}', name: 'app_get_location_price_single', methods: ['GET'])]
    public function retrieveLocationPriceById(int $id, DeliveryLocationPriceRepository $repo,): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Access denied'], 403);
        }

        try {
            $existingLocationPrice = $repo->find($id);

            if (!$existingLocationPrice) {
                return $this->json(['message' => 'Location price not found'], 404);
            }


            return $this->json([
                'message' => 'Price Listed Successfully',
                'order_data' => $existingLocationPrice
            ], 200);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/v1/location-price/{id}', name: 'app_update_location_price', methods: ['PUT'])]
    public function updateLocationPrice(
        int $id,
        Request $request,
        DeliveryLocationPriceRepository $repo,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Access denied'], 403);
        }

        try {
            $existingLocationPrice = $repo->find($id);

            if (!$existingLocationPrice) {
                return $this->json(['message' => 'Location price not found'], 404);
            }

            $data = $request->getContent();
            $serializer->deserialize($data, DeliveryLocationPrice::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $existingLocationPrice]);

            $errors = $validator->validate($existingLocationPrice);

            if (count($errors) > 0) {
                return $this->json(['errors' => (string) $errors], 422);
            }

            $repo->save($existingLocationPrice, true);

            return $this->json([
                'message' => 'Price Updated Successfully',
                'order_data' => $existingLocationPrice
            ], 200);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
