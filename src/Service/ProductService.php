<?php 

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductService
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->validator = $validator;
    }

    /**
     * @return Product[]
     */
    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }

    /**
     * @param int $id
     * @return Product|null
     */
    public function getProduct(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    /**
     * @param array<string, mixed> $data
     * @return Product
     * @throws \InvalidArgumentException
     */
    public function createProduct(array $data): Product
    {
        $product = new Product();

        if (isset($data['name']) && is_string($data['name'])) {
            $product->setName($data['name']);
        }

        if (array_key_exists('description', $data) && (is_string($data['description']) || $data['description'] === null)) {
            $product->setDescription($data['description']);
        } else {
            throw new \InvalidArgumentException('Description must be a string or null');
        }

        if (isset($data['price']) && is_string($data['price'])) {
            $product->setPrice($data['price']);
        }

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Validation errors');
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    /**
     * @param Product $product
     * @param array<string, mixed> $data
     * @return Product
     * @throws \InvalidArgumentException
     */
    public function updateProduct(Product $product, array $data): Product
    {
        if (isset($data['name']) && is_string($data['name'])) {
            $product->setName($data['name']);
        }

        if (array_key_exists('description', $data) && (is_string($data['description']) || $data['description'] === null)) {
            $product->setDescription($data['description']);
        }

        if (isset($data['price']) && is_string($data['price'])) {
            $product->setPrice($data['price']);
        }

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Validation errors');
        }

        $this->entityManager->flush();

        return $product;
    }

    /**
     * @param Product $product
     * @throws \Exception
     */
    public function deleteProduct(Product $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}
