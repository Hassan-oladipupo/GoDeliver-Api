<?php

namespace App\Controller;

use Exception;
use App\Entity\OrderDetails;
use App\Entity\RiderDetails;
use Psr\Log\LoggerInterface;
use App\Entity\DeliveryDetails;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderDetailsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderDetailsController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('api/v1/user-order', name: 'app_order_details', methods: ['POST'])]
    public function createOrder(
        Request $request,
        EntityManagerInterface $entityManger,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(['message' => 'User is not authenticated'], 401);
        }

        $data = $request->getContent();

        try {
            $orderArray = json_decode($data, true);
            $createOrder = $serializer->deserialize($data, OrderDetails::class, 'json');

            if (isset($orderArray['rider'])) {
                $rider = $serializer->deserialize(json_encode($orderArray['rider']), RiderDetails::class, 'json');
                $createOrder->setRider($rider);
                $entityManger->persist($rider);
            }

            $createOrder->setOrderStatus('pending');

            $errors = $validator->validate($createOrder);
            if (count($errors) > 0) {
                return $this->json(['errors' => (string) $errors], 422);
            }

            $createOrder->setUser($this->getUser());

            $entityManger->persist($createOrder);
            $entityManger->flush();

            return $this->json(['message' => 'Order Created Successfully', 'order_data' => $createOrder], 201);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/v1/user-orders', name: 'app_user_orders', methods: ['GET'])]
    public function retrieveUserOrders(Request $request, OrderDetailsRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(['message' => 'User is not authenticated'], 401);
        }

        try {
            $user = $this->getUser();
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', 10);

            $offset = ($page - 1) * $limit;

            $orders = $repo->findBy(['user' => $user], null, $limit, $offset);

            $totalOrders = $repo->count(['user' => $user]);

            $orderData = $serializer->serialize($orders, 'json', [
                AbstractNormalizer::ATTRIBUTES => [
                    'customerInfo', 'customerName', 'pickupContactNo', 'pickupAddress',
                    'riderInfo' => ['id', 'riderName', 'riderContactNo', 'vehicleDetails', 'currentLocation'],
                ],
            ]);

            $response = [
                'total_order' => $totalOrders,
                'page' => $page,
                'limit' => $limit,
                'orders' => json_decode($orderData, true),
            ];

            return $this->json($response, 200);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage());
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    #[Route('/api/v1/all-orders', name: 'app_user_orders', methods: ['GET'])]
    public function retrieveAllOrders(Request $request, OrderDetailsRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Access denied'], 403);
        }

        try {
            $user = $this->getUser();
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', 10);

            $offset = ($page - 1) * $limit;

            $orders = $repo->findAll($limit, $offset);

            $totalOrders = $repo->count($orders);

            $orderData = $serializer->serialize($orders, 'json', [
                AbstractNormalizer::ATTRIBUTES => [
                    'customerInfo', 'customerName', 'pickupContactNo', 'pickupAddress',
                    'riderInfo' => ['id', 'riderName', 'riderContactNo', 'vehicleDetails', 'currentLocation'],
                    'userInfo' => ['id', 'username', 'email'],
                ],
            ]);

            $response = [
                'total_order' => $totalOrders,
                'page' => $page,
                'limit' => $limit,
                'orders' => json_decode($orderData, true),
            ];

            return $this->json($response, 200);
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage());
            return $this->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
