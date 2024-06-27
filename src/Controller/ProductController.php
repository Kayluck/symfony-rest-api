<?php 

namespace App\Controller;

use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Annotation\Route as RouteAttribute;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[RouteAttribute('/api/products')]
class ProductController extends AbstractController
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[RouteAttribute('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();
        return $this->json($products);
    }

    #[RouteAttribute('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !is_array($data)) {
            return $this->json(['message' => 'Invalid JSON body'], 400);
        }

        try {
            $product = $this->productService->createProduct($data);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }

        return $this->json($product, 201);
    }

    #[RouteAttribute('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        return $this->json($product);
    }

    #[RouteAttribute('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $product = $this->productService->getProduct($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data || !is_array($data)) {
            return $this->json(['message' => 'Invalid JSON body'], 400);
        }

        try {
            $updatedProduct = $this->productService->updateProduct($product, $data);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } 

        return $this->json($updatedProduct);
    }

    #[RouteAttribute('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        try {
            $this->productService->deleteProduct($product);
        } catch (\RuntimeException $e) {
            return $this->json(['message' => 'Failed to delete product', 'error' => $e->getMessage()], 500);
        }

        return $this->json(['message' => 'Product deleted']);
    }
}
