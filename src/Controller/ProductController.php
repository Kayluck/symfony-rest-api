<?php 

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route as RouteAttribute;

#[RouteAttribute('/api/products')]
class ProductController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;

    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    #[RouteAttribute('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        return $this->json($products);
    }

    #[RouteAttribute('', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        if (!$data || !is_array($data)) {
            return $this->json(['message' => 'Invalid JSON body'], 400);
        }
    
        $product = new Product();
    
        // Set name if it exists and is a string
        if (isset($data['name']) && is_string($data['name'])) {
            $product->setName($data['name']);
        }
    
        if ($data['description'] === null || is_string($data['description'])) {
            // Handle the case where $data['description'] is null or a string
            $product->setDescription($data['description']);
        } else {
            // Handle the case where $data['description'] is neither null nor a string
            return $this->json(['message' => 'Description must be a string or null'], 400);
        }        
    
        // Set price if it exists and is a string
        if (isset($data['price']) && is_string($data['price'])) {
            $product->setPrice($data['price']);
        }
    
        // Validate the product entity
        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }
    
        // Persist and flush the product entity
        try {
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return $this->json(['message' => 'Failed to create product', 'error' => $e->getMessage()], 500);
        }
    
        return $this->json($product, 201);
    }

    #[RouteAttribute('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        return $this->json($product);
    }

    #[RouteAttribute('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (is_array($data)) {
            if (array_key_exists('name', $data)) {
                $product->setName($data['name']);
            }
            if (array_key_exists('description', $data)) {
                $product->setDescription($data['description']);
            }
            if (array_key_exists('price', $data)) {
                $product->setPrice($data['price']);
            }
        } else {
            return $this->json(['message' => 'Invalid JSON body'], 400);
        }

        // Validate the updated product entity
        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return $this->json(['message' => 'Validation errors', 'errors' => $errors], 400);
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return $this->json(['message' => 'Failed to update product', 'error' => $e->getMessage()], 500);
        }

        return $this->json($product);
    }

    #[RouteAttribute('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json(['message' => 'Product deleted']);
    }
}
