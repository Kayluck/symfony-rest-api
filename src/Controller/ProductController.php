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

/**
 * @Route("/api/products")
 */
class ProductController extends AbstractController
{
    private $entityManager;
    private $productRepository;

    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        return $this->json($products);
    }

    /**
     * @Route("", methods={"POST"})
     */
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json($product, 201);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function show($id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        return $this->json($product);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     */
    public function update($id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $this->entityManager->flush();

        return $this->json($product);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
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
