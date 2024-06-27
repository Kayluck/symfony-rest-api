# Symfony RESTful API with Docker and Static Code Analysis

## Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/Kayluck/symfony-rest-api

2. Build and run the Docker containers:
   ```bash
   docker-compose up --build


3. Run database migrations:
   ```bash
   docker-compose exec app php bin/console doctrine:migrations:migrate

4. Generate JWT keys (if not already generated)
   ```bash
    docker-compose exec app mkdir -p config/jwt
    docker-compose exec app openssl genrsa -out config/jwt/private.pem -aes256 4096
    docker-compose exec app openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

## API Endpoints

`GET /api/products` - List all products.
`POST /api/products` - Create a new product.
`GET /api/products/{id}` - Get details of a single product.
`PUT /api/products/{id}` - Update an existing product.
`DELETE /api/products/{id}` - Delete a product.

## Static Code Analysis
1. Run PHPStan
    ```bash
    docker-compose exec app vendor/bin/phpstan analyse

2. Run PHP_CodeSniffer
    ```bash
    docker-compose exec app vendor/bin/phpcs

## Assumptions
The database uses MySQL.
The application is running in a Docker container.

## Tests
To test the code
   ```bash
   php bin/phpunit
